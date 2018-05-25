<?php

namespace JMS\Serializer;

class DeserializationContext extends Context
{
    private $depth = 0;

    public static function create()
    {
        return new self();
    }

    public function getDirection()
    {
        return GraphNavigator::DIRECTION_DESERIALIZATION;
    }

    public function getDepth()
    {
        return $this->depth;
    }

    public function increaseDepth()
    {
        $this->depth += 1;
    }

    public function decreaseDepth()
    {
        if ($this->depth <= 0) {
            throw new \LogicException('Depth cannot be smaller than zero.');
        }

        $this->depth -= 1;
    }
}
