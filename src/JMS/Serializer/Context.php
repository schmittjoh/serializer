<?php

namespace JMS\Serializer;

use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Exception\LogicException;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Exclusion\GroupsExclusionStrategy;
use JMS\Serializer\Exclusion\VersionExclusionStrategy;
use PhpCollection\Map;

class Context
{
    /**
     * @var \PhpCollection\Map
     */
    public $attributes;

    private $direction;
    private $format;
    private $visitingSet;
    private $visitingStack;

    /** @var VisitorInterface */
    private $visitor;

    /** @var GraphNavigator */
    private $navigator;

    /** @var ExclusionStrategyInterface */
    private $exclusionStrategy;

    /** @var boolean */
    private $serializeNull;

    public static function create()
    {
        return new self();
    }

    public function __construct()
    {
        $this->attributes = new Map();
    }

    public function initialize($direction, $format, VisitorInterface $visitor, GraphNavigator $navigator)
    {
        $this->visitingSet = new \SplObjectStorage();
        $this->visitingStack = new \SplStack();
        $this->direction = $direction;
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

    public function getDirection()
    {
        return $this->direction;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function startVisiting($object)
    {
        if ($this->direction !== GraphNavigator::DIRECTION_SERIALIZATION) {
            return;
        }

        $this->visitingSet->attach($object);
        $this->visitingStack->push($object);
    }

    public function stopVisiting($object)
    {
        if ($this->direction !== GraphNavigator::DIRECTION_SERIALIZATION) {
            return;
        }

        $this->visitingSet->detach($object);
        $poppedObject = $this->visitingStack->pop();

        if ($object !== $poppedObject) {
            throw new RuntimeException('Context visitingStack not working well');
        }
    }

    public function isVisiting($object)
    {
        if (! is_object($object)) {
            throw new LogicException('Expected object but got ' . gettype($object) . '. Do you have the wrong @Type mapping or could this be a Doctrine many-to-many relation?');
        }
        return $this->visitingSet->contains($object);
    }

    public function getPath()
    {
        $path = array();
        foreach ($this->visitingStack as $obj) {
            $path[] = get_class($obj);
        }

        if ( ! $path) {
            return null;
        }

        return implode(' -> ', $path);
    }

    public function getDepth()
    {
        return $this->visitingStack->count();
    }

    public function getObject()
    {
        return !$this->visitingStack->isEmpty() ? $this->visitingStack->top() : null;
    }
}
