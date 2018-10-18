<?php

declare(strict_types=1);

namespace JMS\Serializer;

use JMS\Serializer\Exception\NotAcceptableException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Visitor\SerializationVisitorInterface;

final class JsonSerializationVisitor extends AbstractVisitor implements SerializationVisitorInterface
{
    /**
     * @var int
     */
    private $options = JSON_PRESERVE_ZERO_FRACTION;

    /**
     * @var array
     */
    private $dataStack = [];
    /**
     * @var \ArrayObject
     */
    private $data;

    public function __construct(
        int $options = JSON_PRESERVE_ZERO_FRACTION
    ) {
        $this->dataStack = [];
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function visitNull($data, array $type)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function visitString(string $data, array $type)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function visitBoolean(bool $data, array $type)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function visitInteger(int $data, array $type)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function visitDouble(float $data, array $type)
    {
        return $data;
    }

    /**
     * @param array $data
     * @param array $type
     *
     * @return array|\ArrayObject
     */
    public function visitArray(array $data, array $type)
    {
        \array_push($this->dataStack, $data);

        $rs = isset($type['params'][1]) ? new \ArrayObject() : [];

        $isList = isset($type['params'][0]) && !isset($type['params'][1]);

        $elType = $this->getElementType($type);
        foreach ($data as $k => $v) {
            try {
                $v = $this->navigator->accept($v, $elType);
            } catch (NotAcceptableException $e) {
                continue;
            }

            if ($isList) {
                $rs[] = $v;
            } else {
                $rs[$k] = $v;
            }
        }

        \array_pop($this->dataStack);
        return $rs;
    }

    public function startVisitingObject(ClassMetadata $metadata, object $data, array $type): void
    {
        \array_push($this->dataStack, $this->data);
        $this->data = true === $metadata->isMap ? new \ArrayObject() : [];
    }

    /**
     * @return array|\ArrayObject
     */
    public function endVisitingObject(ClassMetadata $metadata, object $data, array $type)
    {
        $rs = $this->data;
        $this->data = \array_pop($this->dataStack);

        if (true !== $metadata->isList && empty($rs)) {
            return new \ArrayObject();
        }

        return $rs;
    }

    /**
     * {@inheritdoc}
     */
    public function visitProperty(PropertyMetadata $metadata, $v): void
    {
        try {
            $v = $this->navigator->accept($v, $metadata->type);
        } catch (NotAcceptableException $e) {
            return;
        }

        if (true === $metadata->skipWhenEmpty && ($v instanceof \ArrayObject || \is_array($v)) && 0 === count($v)) {
            return;
        }

        if ($metadata->inline) {
            if (\is_array($v) || ($v instanceof \ArrayObject)) {
                // concatenate the two array-like structures
                // is there anything faster?
                foreach ($v as $key => $value) {
                    $this->data[$key] = $value;
                }
            }
        } else {
            $this->data[$metadata->serializedName] = $v;
        }
    }

    /**
     * @deprecated Will be removed in 3.0
     *
     * Checks if some data key exists.
     */
    public function hasData(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * @deprecated Use visitProperty(new StaticPropertyMetadata(null, 'name', 'value'), null) instead
     *
     * Allows you to replace existing data on the current object element.
     *
     * @param mixed $value This value must either be a regular scalar, or an array.
     *                                                       It must not contain any objects anymore.
     */
    public function setData(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getResult($data)
    {
        $result = @json_encode($data, $this->options);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $result;

            case JSON_ERROR_UTF8:
                throw new RuntimeException('Your data could not be encoded because it contains invalid UTF8 characters.');

            default:
                throw new RuntimeException(sprintf('An error occurred while encoding your data (error code %d).', json_last_error()));
        }
    }
}
