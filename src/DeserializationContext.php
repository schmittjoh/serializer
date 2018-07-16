<?php

declare(strict_types=1);

namespace JMS\Serializer;

use JMS\Serializer\Exception\LogicException;

class DeserializationContext extends Context
{
    /**
     * @var int
     */
    private $depth = 0;

    public static function create(): self
    {
        return new self();
    }

    public function getDirection(): int
    {
        return GraphNavigatorInterface::DIRECTION_DESERIALIZATION;
    }

    public function getDepth(): int
    {
        return $this->depth;
    }

    public function increaseDepth(): void
    {
        $this->depth += 1;
    }

    public function decreaseDepth(): void
    {
        if ($this->depth <= 0) {
            throw new LogicException('Depth cannot be smaller than zero.');
        }

        $this->depth -= 1;
    }
}
