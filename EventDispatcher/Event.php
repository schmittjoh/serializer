<?php

namespace JMS\SerializerBundle\EventDispatcher;

use JMS\SerializerBundle\Serializer\VisitorInterface;

use JMS\SerializerBundle\Metadata\ClassMetadata;

class Event
{
    private $object;
    private $visitor;
    private $classMetadata;
    private $preventDefault = false;

    public function __construct(VisitorInterface $visitor, $object, ClassMetadata $classMetadata)
    {
        $this->visitor = $visitor;
        $this->object = $object;
        $this->classMetadata = $classMetadata;
    }

    public function getVisitor()
    {
        return $this->visitor;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function getClassMetadata()
    {
        return $this->classMetadata;
    }
}