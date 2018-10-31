<?php

declare(strict_types=1);

namespace JMS\Serializer;

use JMS\Serializer\Exception\RuntimeException;
use Metadata\MetadataFactoryInterface;

class SerializationContext extends Context
{
    /** @var \SplObjectStorage */
    private $visitingSet;

    /** @var \SplStack */
    private $visitingStack;

    /**
     * @var string
     */
    private $initialType;

    /**
     * @var bool
     */
    private $serializeNull = false;

    public static function create(): self
    {
        return new self();
    }

    public function initialize(string $format, VisitorInterface $visitor, GraphNavigatorInterface $navigator, MetadataFactoryInterface $factory): void
    {
        parent::initialize($format, $visitor, $navigator, $factory);

        $this->visitingSet = new \SplObjectStorage();
        $this->visitingStack = new \SplStack();
    }

    /**
     * Set if NULLs should be serialized (TRUE) ot not (FALSE)
     */
    public function setSerializeNull(bool $bool): self
    {
        $this->serializeNull = $bool;

        return $this;
    }

    /**
     * Returns TRUE when NULLs should be serialized
     * Returns FALSE when NULLs should not be serialized
     */
    public function shouldSerializeNull(): bool
    {
        return $this->serializeNull;
    }

    /**
     * @param mixed $object
     */
    public function startVisiting($object): void
    {
        if (!\is_object($object)) {
            return;
        }
        $this->visitingSet->attach($object);
        $this->visitingStack->push($object);
    }

    /**
     * @param mixed $object
     */
    public function stopVisiting($object): void
    {
        if (!\is_object($object)) {
            return;
        }
        $this->visitingSet->detach($object);
        $poppedObject = $this->visitingStack->pop();

        if ($object !== $poppedObject) {
            throw new RuntimeException('Context visitingStack not working well');
        }
    }

    /**
     * @param mixed $object
     */
    public function isVisiting($object): bool
    {
        if (!\is_object($object)) {
            return false;
        }

        return $this->visitingSet->contains($object);
    }

    public function getPath(): ?string
    {
        $path = [];
        foreach ($this->visitingStack as $obj) {
            $path[] = \get_class($obj);
        }

        if (!$path) {
            return null;
        }

        return implode(' -> ', $path);
    }

    public function getDirection(): int
    {
        return GraphNavigatorInterface::DIRECTION_SERIALIZATION;
    }

    public function getDepth(): int
    {
        return $this->visitingStack->count();
    }

    public function getObject(): ?object
    {
        return !$this->visitingStack->isEmpty() ? $this->visitingStack->top() : null;
    }

    public function getVisitingStack(): \SplStack
    {
        return $this->visitingStack;
    }


    public function getVisitingSet(): \SplObjectStorage
    {
        return $this->visitingSet;
    }

    /**
     * @return $this
     */
    public function setInitialType(string $type): self
    {
        $this->initialType = $type;
        $this->setAttribute('initial_type', $type);
        return $this;
    }

    public function getInitialType(): ?string
    {
        return $this->initialType
            ? $this->initialType
            : $this->hasAttribute('initial_type') ? $this->getAttribute('initial_type') : null;
    }
}
