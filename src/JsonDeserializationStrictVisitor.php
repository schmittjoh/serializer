<?php

declare(strict_types=1);

namespace JMS\Serializer;

use JMS\Serializer\Exception\NonVisitableTypeException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;

use function is_float;
use function is_int;
use function is_string;

final class JsonDeserializationStrictVisitor extends AbstractVisitor implements DeserializationVisitorInterface
{
    /** @var JsonDeserializationVisitor */
    private $wrappedDeserializationVisitor;

    public function __construct(
        int $options = 0,
        int $depth = 512
    ) {
        $this->wrappedDeserializationVisitor = new JsonDeserializationVisitor($options, $depth);
    }

    public function setNavigator(GraphNavigatorInterface $navigator): void
    {
        parent::setNavigator($navigator);

        $this->wrappedDeserializationVisitor->setNavigator($navigator);
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
    public function visitString($data, array $type): ?string
    {
        if (null === $data) {
            return null;
        }

        if (! is_string($data)) {
            throw NonVisitableTypeException::fromDataAndType($data, $type);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function visitBoolean($data, array $type): ?bool
    {
        if (null === $data) {
            return null;
        }

        if (! is_bool($data)) {
            throw NonVisitableTypeException::fromDataAndType($data, $type);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function visitInteger($data, array $type): ?int
    {
        if (null === $data) {
            return null;
        }

        if (! is_int($data)) {
            throw NonVisitableTypeException::fromDataAndType($data, $type);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function visitDouble($data, array $type): ?float
    {
        if (null === $data) {
            return null;
        }

        if (! is_float($data)) {
            throw NonVisitableTypeException::fromDataAndType($data, $type);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function visitArray($data, array $type): array
    {
        try {
            return $this->wrappedDeserializationVisitor->visitArray($data, $type);
        } catch (RuntimeException $e) {
            throw NonVisitableTypeException::fromDataAndType($data, $type, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function visitDiscriminatorMapProperty($data, ClassMetadata $metadata): string
    {
        return $this->wrappedDeserializationVisitor->visitDiscriminatorMapProperty($data, $metadata);
    }

    /**
     * {@inheritdoc}
     */
    public function startVisitingObject(ClassMetadata $metadata, object $object, array $type): void
    {
        $this->wrappedDeserializationVisitor->startVisitingObject($metadata, $object, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function visitProperty(PropertyMetadata $metadata, $data)
    {
        return $this->wrappedDeserializationVisitor->visitProperty($metadata, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function endVisitingObject(ClassMetadata $metadata, $data, array $type): object
    {
        return $this->wrappedDeserializationVisitor->endVisitingObject($metadata, $data, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getResult($data)
    {
        return $this->wrappedDeserializationVisitor->getResult($data);
    }

    public function getCurrentObject(): ?object
    {
        return $this->wrappedDeserializationVisitor->getCurrentObject();
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($data)
    {
        return $this->wrappedDeserializationVisitor->prepare($data);
    }
}
