<?php

declare(strict_types=1);

namespace JMS\Serializer;

use JMS\Serializer\Exception\LogicException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use const JSON_ERROR_CTRL_CHAR;
use const JSON_ERROR_DEPTH;
use const JSON_ERROR_NONE;
use const JSON_ERROR_STATE_MISMATCH;
use const JSON_ERROR_SYNTAX;
use const JSON_ERROR_UTF8;
use function array_key_exists;
use function count;
use function gettype;
use function is_array;
use function json_decode;
use function json_encode;
use function json_last_error;
use function sprintf;

final class JsonDeserializationVisitor extends AbstractVisitor implements DeserializationVisitorInterface
{
    private $options = 0;
    private $depth   = 512;

    private $objectStack;
    private $currentObject;

    public function __construct(
        int $options = 0,
        int $depth = 512
    ) {
        $this->objectStack = new \SplStack();
        $this->options     = $options;
        $this->depth       = $depth;
    }

    public function visitNull($data, array $type): void
    {
    }

    public function visitString($data, array $type): string
    {
        return (string) $data;
    }

    public function visitBoolean($data, array $type): bool
    {
        return (bool) $data;
    }

    public function visitInteger($data, array $type): int
    {
        return (int) $data;
    }

    public function visitDouble($data, array $type): float
    {
        return (float) $data;
    }

    public function visitArray($data, array $type): array
    {
        if (!is_array($data)) {
            throw new RuntimeException(sprintf('Expected array, but got %s: %s', gettype($data), json_encode($data)));
        }

        // If no further parameters were given, keys/values are just passed as is.
        if (!$type['params']) {
            return $data;
        }

        switch (count($type['params'])) {
            case 1: // Array is a list.
                $listType = $type['params'][0];

                $result = [];

                foreach ($data as $v) {
                    $result[] = $this->navigator->accept($v, $listType);
                }

                return $result;

            case 2: // Array is a map.
                list($keyType, $entryType) = $type['params'];

                $result = [];

                foreach ($data as $k => $v) {
                    $result[$this->navigator->accept($k, $keyType)] = $this->navigator->accept($v, $entryType);
                }

                return $result;

            default:
                throw new RuntimeException(sprintf('Array type cannot have more than 2 parameters, but got %s.', json_encode($type['params'])));
        }
    }

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

    public function startVisitingObject(ClassMetadata $metadata, object $object, array $type): void
    {
        $this->setCurrentObject($object);
    }

    public function visitProperty(PropertyMetadata $metadata, $data): void
    {
        $name = $metadata->serializedName;

        if (null === $data) {
            return;
        }

        if (!is_array($data)) {
            throw new RuntimeException(sprintf('Invalid data %s (%s), expected "%s".', json_encode($data), $metadata->type['name'], $metadata->class));
        }

        if (!array_key_exists($name, $data)) {
            return;
        }

        if (!$metadata->type) {
            throw new RuntimeException(sprintf('You must define a type for %s::$%s.', $metadata->class, $metadata->name));
        }

        $v = $data[$name] !== null ? $this->navigator->accept($data[$name], $metadata->type) : null;

        return $v;
    }

    public function endVisitingObject(ClassMetadata $metadata, $data, array $type): object
    {
        $obj = $this->currentObject;
        $this->revertCurrentObject();

        return $obj;
    }

    public function getResult($data)
    {
        return $data;
    }

    public function setCurrentObject($object): void
    {
        $this->objectStack->push($this->currentObject);
        $this->currentObject = $object;
    }

    public function getCurrentObject()
    {
        return $this->currentObject;
    }

    public function revertCurrentObject()
    {
        return $this->currentObject = $this->objectStack->pop();
    }

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
