<?php

declare(strict_types=1);

namespace JMS\Serializer;

use Metadata\MetadataFactoryInterface;

class SerializationContext extends Context
{
    /** @var \SplObjectStorage */
    private $visitingSet;

    /** @var object[] */
    private array $visitingStack = [];

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
        $this->visitingStack = [];
    }

    /**
     * Set if NULLs should be serialized (TRUE) ot not (FALSE)
     */
    public function setSerializeNull(bool $bool): self
    {
        $this->assertMutable();

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

        $this->visitingSet->offsetSet($object);
        $this->visitingStack[] = $object;
    }

    /**
     * @param mixed $object
     */
    public function stopVisiting($object): void
    {
        if (!\is_object($object)) {
            return;
        }

        $this->visitingSet->offsetUnset($object);
        array_pop($this->visitingStack);
    }

    /**
     * @param mixed $object
     */
    public function isVisiting($object): bool
    {
        if (!\is_object($object)) {
            return false;
        }

        return $this->visitingSet->offsetExists($object);
    }

    public function getPath(): ?string
    {
        if (!$this->visitingStack) {
            return null;
        }

        $path = [];
        foreach ($this->visitingStack as $obj) {
            $path[] = \get_class($obj);
        }

        return implode(' -> ', $path);
    }

    public function getDirection(): int
    {
        return GraphNavigatorInterface::DIRECTION_SERIALIZATION;
    }

    public function getDepth(): int
    {
        return \count($this->visitingStack);
    }

    public function getObject(): ?object
    {
        $n = \count($this->visitingStack);

        return $n > 0 ? $this->visitingStack[$n - 1] : null;
    }

    /**
     * @return object[]
     */
    public function getVisitingStack(): array
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
        $this->assertMutable();

        $this->initialType = $type;
        $this->setAttribute('initial_type', $type);

        return $this;
    }

    public function getInitialType(): ?string
    {
        return $this->initialType ?: ($this->hasAttribute('initial_type') ? $this->getAttribute('initial_type') : null);
    }
}
