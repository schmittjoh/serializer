<?php

namespace JMS\Serializer;

use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Exception\LogicException;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Exclusion\GroupsExclusionStrategy;
use JMS\Serializer\Exclusion\VersionExclusionStrategy;
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

    /** @var ExclusionStrategyInterface */
    private $exclusionStrategy;

    /** @var boolean */
    private $serializeNull;

    public function __construct()
    {
        $this->attributes = new Map();
    }

    public function initialize($format, VisitorInterface $visitor, GraphNavigator $navigator)
    {
        $this->format = $format;
        $this->visitor = $visitor;
        $this->navigator = $navigator;
    }

    public function accept($data, array $type)
    {
        return $this->navigator->accept($data, $type, $this);
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
