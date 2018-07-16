<?php

declare(strict_types=1);

namespace JMS\Serializer;

use JMS\Serializer\Exception\LogicException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Exclusion\DepthExclusionStrategy;
use JMS\Serializer\Exclusion\DisjunctExclusionStrategy;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Exclusion\GroupsExclusionStrategy;
use JMS\Serializer\Exclusion\VersionExclusionStrategy;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use Metadata\MetadataFactory;
use Metadata\MetadataFactoryInterface;

abstract class Context
{
    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var string
     */
    private $format;

    /**
     * @var VisitorInterface
     */
    private $visitor;

    /**
     * @var GraphNavigatorInterface
     */
    private $navigator;

    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    /** @var DisjunctExclusionStrategy */
    private $exclusionStrategy;

    /**
     * @var bool
     */
    private $serializeNull = false;

    /**
     * @var bool
     */
    private $initialized = false;

    /** @var \SplStack */
    private $metadataStack;

    public function __construct()
    {
    }

    public function initialize(string $format, VisitorInterface $visitor, GraphNavigatorInterface $navigator, MetadataFactoryInterface $factory): void
    {
        if ($this->initialized) {
            throw new LogicException('This context was already initialized, and cannot be re-used.');
        }

        $this->format = $format;
        $this->visitor = $visitor;
        $this->navigator = $navigator;
        $this->metadataFactory = $factory;
        $this->metadataStack = new \SplStack();

        if (isset($this->attributes['groups'])) {
            $this->addExclusionStrategy(new GroupsExclusionStrategy($this->attributes['groups']));
        }

        if (isset($this->attributes['version'])) {
            $this->addExclusionStrategy(new VersionExclusionStrategy($this->attributes['version']));
        }

        if (!empty($this->attributes['max_depth_checks'])) {
            $this->addExclusionStrategy(new DepthExclusionStrategy());
        }

        $this->initialized = true;
    }

    public function getMetadataFactory(): MetadataFactoryInterface
    {
        return $this->metadataFactory;
    }

    public function getVisitor(): VisitorInterface
    {
        return $this->visitor;
    }

    public function getNavigator(): GraphNavigatorInterface
    {
        return $this->navigator;
    }

    public function getExclusionStrategy(): ?ExclusionStrategyInterface
    {
        return $this->exclusionStrategy;
    }

    /**
     * @return mixed
     */
    public function getAttribute(string $key)
    {
        return $this->attributes[$key];
    }

    public function hasAttribute(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * @param mixed $value
     */
    public function setAttribute(string $key, $value): self
    {
        $this->assertMutable();
        $this->attributes[$key] = $value;

        return $this;
    }

    private function assertMutable(): void
    {
        if (!$this->initialized) {
            return;
        }

        throw new LogicException('This context was already initialized and is immutable; you cannot modify it anymore.');
    }

    public function addExclusionStrategy(ExclusionStrategyInterface $strategy): self
    {
        $this->assertMutable();

        if (null === $this->exclusionStrategy) {
            $this->exclusionStrategy = $strategy;
            return $this;
        }

        if ($this->exclusionStrategy instanceof DisjunctExclusionStrategy) {
            $this->exclusionStrategy->addStrategy($strategy);
            return $this;
        }

        $this->exclusionStrategy = new DisjunctExclusionStrategy([
            $this->exclusionStrategy,
            $strategy,
        ]);

        return $this;
    }

    public function setVersion(string $version): self
    {
        $this->attributes['version'] = $version;

        return $this;
    }

    /**
     * @param array|string $groups
     */
    public function setGroups($groups): self
    {
        if (empty($groups)) {
            throw new LogicException('The groups must not be empty.');
        }

        $this->attributes['groups'] = (array) $groups;

        return $this;
    }

    public function enableMaxDepthChecks(): self
    {
        $this->attributes['max_depth_checks'] = true;

        return $this;
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
     *
     */
    public function shouldSerializeNull(): bool
    {
        return $this->serializeNull;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function pushClassMetadata(ClassMetadata $metadata): void
    {
        $this->metadataStack->push($metadata);
    }

    public function pushPropertyMetadata(PropertyMetadata $metadata): void
    {
        $this->metadataStack->push($metadata);
    }

    public function popPropertyMetadata(): void
    {
        $metadata = $this->metadataStack->pop();

        if (!$metadata instanceof PropertyMetadata) {
            throw new RuntimeException('Context metadataStack not working well');
        }
    }

    public function popClassMetadata(): void
    {
        $metadata = $this->metadataStack->pop();

        if (!$metadata instanceof ClassMetadata) {
            throw new RuntimeException('Context metadataStack not working well');
        }
    }

    public function getMetadataStack(): \SplStack
    {
        return $this->metadataStack;
    }

    /**
     * @return array
     */
    public function getCurrentPath(): array
    {
        if (!$this->metadataStack) {
            return [];
        }

        $paths = [];
        foreach ($this->metadataStack as $metadata) {
            if ($metadata instanceof PropertyMetadata) {
                array_unshift($paths, $metadata->name);
            }
        }

        return $paths;
    }

    abstract public function getDepth(): int;

    abstract public function getDirection(): int;
}
