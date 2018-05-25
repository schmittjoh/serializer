<?php

namespace JMS\Serializer\EventDispatcher;

use JMS\Serializer\Context;

class ObjectEvent extends Event
{
    private $object;

    public function __construct(Context $context, $object, array $type)
    {
        parent::__construct($context, $type);

        $this->object = $object;
    }

    public function getObject()
    {
        return $this->object;
    }
}
