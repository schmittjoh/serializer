<?php

namespace JMS\Serializer\EventDispatcher;

use JMS\Serializer\VisitorInterface;

class PreDeserializeEvent extends Event
{
  protected $data;

    public function __construct(VisitorInterface $visitor, &$data, array $type)
    {
        $this->visitor = $visitor;
        $this->data = &$data;
        $this->type = $type;
    }

    public function getObject()
    {
        return $this->data;
    }

    public function setValue($field, $value)
    {
        $this->data[$field] = $value;
    }
}
