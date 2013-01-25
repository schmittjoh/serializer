<?php

namespace JMS\Serializer\EventDispatcher;

use JMS\Serializer\VisitorInterface;

class Event
{
    protected $type;
    private $object;
    private $visitor;

    public function __construct(VisitorInterface $visitor, $object, array $type)
    {
        $this->visitor = $visitor;
        $this->object = $object;
        $this->type = $type;
    }

    public function getVisitor()
    {
        return $this->visitor;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getObject()
    {
        return $this->object;
    }
}
