<?php

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

    public static function create()
    {
        return new self();
    }

    /**
     * @param string $format
     */
    public function initialize($format, VisitorInterface $visitor, GraphNavigator $navigator, MetadataFactoryInterface $factory)
    {
        parent::initialize($format, $visitor, $navigator, $factory);

        $this->visitingSet = new \SplObjectStorage();
        $this->visitingStack = new \SplStack();
    }

    public function startVisiting($object)
    {
        if (!\is_object($object)) {
            return;
        }
        $this->visitingSet->attach($object);
        $this->visitingStack->push($object);
    }

    public function stopVisiting($object)
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

    public function isVisiting($object)
    {
        if (!\is_object($object)) {
            return false;
        }

        return $this->visitingSet->contains($object);
    }

    public function getPath()
    {
        $path = array();
        foreach ($this->visitingStack as $obj) {
            $path[] = \get_class($obj);
        }

        if (!$path) {
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

    public function getVisitingStack()
    {
        return $this->visitingStack;
    }

    public function getVisitingSet()
    {
        return $this->visitingSet;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setInitialType($type)
    {
        $this->initialType = $type;
        $this->attributes->set('initial_type', $type);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getInitialType()
    {
        return $this->initialType
            ? $this->initialType
            : ($this->attributes->containsKey('initial_type') ? $this->attributes->get('initial_type')->get() : null);
    }
}
