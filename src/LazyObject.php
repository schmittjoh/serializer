<?php

namespace JMS\Serializer;

class LazyObject implements \JsonSerializable
{
    /**
     * @var callable
     */
    private $serialisation;
    private $params;


    public function __construct(callable $serialisation, ...$params)
    {
        $this->serialisation = $serialisation;
        $this->params = $params;
    }

    public function jsonSerialize()
    {
        $callback = $this->serialisation;
        return $callback(...$this->params);
    }
}