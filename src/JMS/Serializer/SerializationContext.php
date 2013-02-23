<?php

namespace JMS\Serializer;

use JMS\Serializer\Exception\LogicException;
use JMS\Serializer\Exception\RuntimeException;

class SerializationContext extends Context
{
    /** @var \SplObjectStorage */
    private $visitingSet;

    /** @var \SplStack */
    private $visitingStack;

    public static function create()
    {
        return new self();
    }

    public function initialize($format, VisitorInterface $visitor, GraphNavigator $navigator)
    {
        parent::initialize($format, $visitor, $navigator);

        $this->visitingSet = new \SplObjectStorage();
        $this->visitingStack = new \SplStack();
    }

    public function startVisiting($object)
    {
        $this->visitingSet->attach($object);
        $this->visitingStack->push($object);
    }

    public function stopVisiting($object)
    {
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

    public function getDirection()
    {
        return GraphNavigator::DIRECTION_SERIALIZATION;
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