<?php

namespace JMS\Serializer\EventDispatcher;

use JMS\Serializer\DeserializationContext;

class PreDeserializeEvent extends Event
{
    private $data;

    public function __construct(DeserializationContext $context, $data, array $type)
    {
        parent::__construct($context, $type);

        $this->data = $data;
    }

    public function setType($name, array $params = array())
    {
        $this->type = array('name' => $name, 'params' => $params);
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }
}
