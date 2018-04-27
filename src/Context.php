<?php

declare(strict_types=1);

/*
 * Copyright 2016 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
    private $attributes = array();

    private $format;

    /** @var SerializationVisitorInterface|DeserializationVisitorInterface */
    private $visitor;

    /** @var GraphNavigatorInterface */
    private $navigator;

    /** @var MetadataFactory */
    private $metadataFactory;

    /** @var DisjunctExclusionStrategy */
    private $exclusionStrategy;

    /** @var boolean */
    private $serializeNull = false;

    private $initialized = false;

    /** @var \SplStack */
    private $metadataStack;

    public function __construct()
    {
        $this->exclusionStrategy = new DisjunctExclusionStrategy();
    }

    /**
     * @param string $format
     */
    public function initialize(string $format, $visitor, GraphNavigatorInterface $navigator, MetadataFactoryInterface $factory): void
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

    public function getVisitor()
    {
        return $this->visitor;
    }

    public function getNavigator(): GraphNavigatorInterface
    {
        return $this->navigator;
    }

    public function getExclusionStrategy(): ExclusionStrategyInterface
    {
        return $this->exclusionStrategy;
    }

    public function getAttribute(string $key)
    {
        return $this->attributes[$key];
    }

    public function hasAttribute(string $key)
    {
        return isset($this->attributes[$key]);
    }

    public function setAttribute(string $key, $value)
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

        $this->exclusionStrategy->addStrategy($strategy);

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

        $this->attributes['groups'] = (array)$groups;

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
     * Returns NULL when NULLs should not be serialized,
     * but the user has not explicitly decided to use this policy
     *
     * @return bool
     */
    public function shouldSerializeNull(): bool
    {
        return $this->serializeNull;
    }

    /**
     * @return string
     */
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
    public function getCurrentPath()
    {
        if (!$this->metadataStack) {
            return array();
        }

        $paths = array();
        foreach ($this->metadataStack as $metadata) {
            if ($metadata instanceof PropertyMetadata) {
                array_unshift($paths, $metadata->name);
            }
        }

        return $paths;
    }


    abstract public function getDepth(): int;

    /**
     * @return integer
     */
    abstract public function getDirection(): int;
}
