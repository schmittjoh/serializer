<?php

namespace JMS\Serializer\EventDispatcher;

use JMS\Serializer\Context;

class Event
{
    protected $type;
    private $context;

    public function __construct(Context $context, array $type)
    {
        $this->context = $context;
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
}
