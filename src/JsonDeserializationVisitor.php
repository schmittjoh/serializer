<?php

declare(strict_types=1);

namespace JMS\Serializer;

use JMS\Serializer\Exception\LogicException;
use JMS\Serializer\Exception\NotAcceptableException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;

final class JsonDeserializationVisitor extends AbstractVisitor implements DeserializationVisitorInterface
{
    /**
     * @var int
     */
    private $options = 0;

    /**
     * @var int
     */
    private $depth = 512;

    /**
     * @var \SplStack
     */
    private $objectStack;

    /**
     * @var object|null
     */
    private $currentObject;

    public function __construct(
        int $options = 0,
        int $depth = 512
    ) {
        $this->objectStack = new \SplStack();
        $this->options = $options;
        $this->depth = $depth;
    }

    /**
     * {@inheritdoc}
     */
    public function visitNull($data, array $type): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function visitString($data, array $type): string
    {
        return (string) $data;
    }

    /**
     * {@inheritdoc}
     */
    public function visitBoolean($data, array $type): bool
    {
        return (bool) $data;
    }

    /**
     * {@inheritdoc}
     */
    public function visitInteger($data, array $type): int
    {
        return (int) $data;
    }

    /**
     * {@inheritdoc}
     */
    public function visitDouble($data, array $type): float
    {
        return (float) $data;
    }

    /**
     * {@inheritdoc}
     */
    public function visitArray($data, array $type): array
    {
        if (!\is_array($data)) {
            throw new RuntimeException(sprintf('Expected array, but got %s: %s', \gettype($data), json_encode($data)));
        }

        // If no further parameters were given, keys/values are just passed as is.
        if (!$type['params']) {
            return $data;
        }

        switch (\count($type['params'])) {
            case 1: // Array is a list.
                $listType = $type['params'][0];

                $result = [];

                foreach ($data as $v) {
                    $result[] = $this->navigator->accept($v, $listType);
                }

                return $result;

            case 2: // Array is a map.
                [$keyType, $entryType] = $type['params'];

                $result = [];

                foreach ($data as $k => $v) {
                    $result[$this->navigator->accept($k, $keyType)] = $this->navigator->accept($v, $entryType);
                }

                return $result;

            default:
                throw new RuntimeException(sprintf('Array type cannot have more than 2 parameters, but got %s.', json_encode($type['params'])));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function visitDiscriminatorMapProperty($data, ClassMetadata $metadata): string
    {
        if (isset($data[$metadata->discriminatorFieldName])) {
            return (string) $data[$metadata->discriminatorFieldName];
        }

        throw new LogicException(sprintf(
            'The discriminator field name "%s" for base-class "%s" was not found in input data.',
            $metadata->discriminatorFieldName,
            $metadata->name
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function startVisitingObject(ClassMetadata $metadata, object $object, array $type): void
    {
        $this->setCurrentObject($object);
    }

    /**
     * {@inheritdoc}
     */
    public function visitProperty(PropertyMetadata $metadata, $data)
    {
        $name = $metadata->serializedName;

        if (null === $data) {
            return;
        }

        if (!\is_array($data)) {
            throw new RuntimeException(sprintf('Invalid data %s (%s), expected "%s".', json_encode($data), $metadata->type['name'], $metadata->class));
        }

        if (true === $metadata->inline) {
            if (!$metadata->type) {
                throw RuntimeException::noMetadataForProperty($metadata->class, $metadata->name);
            }
            return $this->navigator->accept($data, $metadata->type);
        }

        if (!array_key_exists($name, $data)) {
            throw new NotAcceptableException();
        }

        if (!$metadata->type) {
            throw RuntimeException::noMetadataForProperty($metadata->class, $metadata->name);
        }

        return null !== $data[$name] ? $this->navigator->accept($data[$name], $metadata->type) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function endVisitingObject(ClassMetadata $metadata, $data, array $type): object
    {
        $obj = $this->currentObject;
        $this->revertCurrentObject();

        return $obj;
    }

    /**
     * {@inheritdoc}
     */
    public function getResult($data)
    {
        return $data;
    }

    public function setCurrentObject(object $object): void
    {
        $this->objectStack->push($this->currentObject);
        $this->currentObject = $object;
    }

    public function getCurrentObject(): ?object
    {
        return $this->currentObject;
    }

    public function revertCurrentObject(): ?object
    {
        return $this->currentObject = $this->objectStack->pop();
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($str)
    {
        $decoded = json_decode($str, true, $this->depth, $this->options);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $decoded;

            case JSON_ERROR_DEPTH:
                throw new RuntimeException('Could not decode JSON, maximum stack depth exceeded.');

            case JSON_ERROR_STATE_MISMATCH:
                throw new RuntimeException('Could not decode JSON, underflow or the nodes mismatch.');

            case JSON_ERROR_CTRL_CHAR:
                throw new RuntimeException('Could not decode JSON, unexpected control character found.');

            case JSON_ERROR_SYNTAX:
                throw new RuntimeException('Could not decode JSON, syntax error - malformed JSON.');

            case JSON_ERROR_UTF8:
                throw new RuntimeException('Could not decode JSON, malformed UTF-8 characters (incorrectly encoded?)');

            default:
                throw new RuntimeException('Could not decode JSON.');
        }
    }
}
