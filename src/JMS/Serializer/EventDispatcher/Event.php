<?php

namespace JMS\Serializer\EventDispatcher;

use JMS\Serializer\Context;

class Event
{
    protected $type;
    private $object;
    private $context;

    public function __construct(Context $context, $object, array $type)
    {
        $this->context = $context;
        $this->object = $object;
        $this->type = $type;
    }

    public function getVisitor()
    {
        return $this->context->getVisitor();
    }

    public function getContext()
    {
        return $this->context;
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
