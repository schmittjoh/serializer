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

    public function __construct()
    {
        $this->attributes = new Map();
    }

    public function initialize($format, VisitorInterface $visitor, GraphNavigator $navigator, MetadataFactoryInterface $factory)
    {
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
        $this->attributes->set($key, $value);

        return $this;
    }

    public function setExclusionStrategy(ExclusionStrategyInterface $strategy)
    {
        $this->exclusionStrategy = $strategy;

        return $this;
    }

    /**
     * @param integer $version
     */
    public function setVersion($version)
    {
        if (null === $version) {
            $this->exclusionStrategy = null;

            return $this;
        }

        $this->exclusionStrategy = new VersionExclusionStrategy($version);

        return $this;
    }

    /**
     * @param null|array $groups
     */
    public function setGroups($groups)
    {
        if ( ! $groups) {
            $this->exclusionStrategy = null;

            return $this;
        }

        $this->exclusionStrategy = new GroupsExclusionStrategy((array) $groups);

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
