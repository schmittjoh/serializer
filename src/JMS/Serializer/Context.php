<?php

/*
 * Copyright 2013 Johannes M. Schmitt <schmittjoh@gmail.com>
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

use JMS\Serializer\Exclusion\DisjunctExclusionStrategy;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Exclusion\GroupsExclusionStrategy;
use JMS\Serializer\Exclusion\VersionExclusionStrategy;
use Metadata\MetadataFactory;
use Metadata\MetadataFactoryInterface;
use PhpCollection\Map;

abstract class Context
{
    /**
     * @var \PhpCollection\Map
     */
    public $attributes;

    private $format;

    /** @var VisitorInterface */
    private $visitor;

    /** @var GraphNavigator */
    private $navigator;

    /** @var MetadataFactory */
    private $metadataFactory;

    /** @var ExclusionStrategyInterface */
    private $exclusionStrategy;

    /** @var boolean */
    private $serializeNull;

    private $initialized = false;

    public function __construct()
    {
        $this->attributes = new Map();
    }

    public function initialize($format, VisitorInterface $visitor, GraphNavigator $navigator, MetadataFactoryInterface $factory)
    {
        if ($this->initialized) {
            throw new \LogicException('This context was already initialized, and cannot be re-used.');
        }

        $this->initialized = true;
        $this->format = $format;
        $this->visitor = $visitor;
        $this->navigator = $navigator;
        $this->metadataFactory = $factory;
    }

    public function accept($data, array $type)
    {
        return $this->navigator->accept($data, $type, $this);
    }

    public function getMetadataFactory()
    {
        return $this->metadataFactory;
    }

    public function getVisitor()
    {
        return $this->visitor;
    }

    public function getNavigator()
    {
        return $this->navigator;
    }

    public function getExclusionStrategy()
    {
        return $this->exclusionStrategy;
    }

    public function setAttribute($key, $value)
    {
        $this->assertMutable();
        $this->attributes->set($key, $value);

        return $this;
    }

    private function assertMutable()
    {
        if ( ! $this->initialized) {
            return;
        }

        throw new \LogicException('This context was already initialized and is immutable; you cannot modify it anymore.');
    }

    public function addExclusionStrategy(ExclusionStrategyInterface $strategy)
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

        $this->exclusionStrategy = new DisjunctExclusionStrategy(array(
            $this->exclusionStrategy,
            $strategy,
        ));

        return $this;
    }

    /**
     * @param integer $version
     */
    public function setVersion($version)
    {
        if (null === $version) {
            throw new \LogicException('The version must not be null.');
        }

        $this->attributes->set('version', $version);
        $this->addExclusionStrategy(new VersionExclusionStrategy($version));

        return $this;
    }

    /**
     * @param array|string $groups
     */
    public function setGroups($groups)
    {
        if (empty($groups)) {
            throw new \LogicException('The groups must not be empty.');
        }

        $this->attributes->set('groups', (array) $groups);
        $this->addExclusionStrategy(new GroupsExclusionStrategy((array) $groups));

        return $this;
    }

    public function setSerializeNull($bool)
    {
        $this->serializeNull = (boolean) $bool;

        return $this;
    }

    public function shouldSerializeNull()
    {
        return $this->serializeNull;
    }

    public function getFormat()
    {
        return $this->format;
    }

    abstract public function getDepth();
    abstract public function getDirection();
}
