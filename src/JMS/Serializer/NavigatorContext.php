<?php

namespace JMS\Serializer;

use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Exception\LogicException;

class NavigatorContext
{
    private $direction;
    private $format;
    private $visitingSet;
    private $visitingStack;

    public function __construct($direction, $format)
    {
        $this->direction = $direction;
        $this->format = $format;
        $this->visitingSet = new \SplObjectStorage();
        $this->visitingStack = new \SplStack();
    }

    public function getDirection()
    {
        return $this->direction;
    }

    public function isSerializing()
    {
        return $this->direction === GraphNavigator::DIRECTION_SERIALIZATION;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function startVisiting($object)
    {
        if (!$this->isSerializing()) {
            return;
        }

        $this->visitingSet->attach($object);
        $this->visitingStack->push($object);
    }

    public function stopVisiting($object)
    {
        if (!$this->isSerializing()) {
            return;
        }

        $this->visitingSet->detach($object);
        $poppedObject = $this->visitingStack->pop();

        if ($object !== $poppedObject) {
            throw new RuntimeException('NavigatorContext visitingStack not working well');
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
