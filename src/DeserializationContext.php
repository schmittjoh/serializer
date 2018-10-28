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

    /**
     * @var bool
     */
    private $deserializeNull = false;

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

    /**
     * Set if NULLs should be deserialized (TRUE) ot not (FALSE)
     */
    public function setDeserializeNull(bool $bool): self
    {
        $this->deserializeNull = $bool;

        return $this;
    }

    /**
     * Returns TRUE when NULLs should be deserialized
     * Returns FALSE when NULLs should not be deserialized
     */
    public function shouldDeserializeNull(): bool
    {
        return $this->deserializeNull;
    }

    public function decreaseDepth(): void
    {
        if ($this->depth <= 0) {
            throw new LogicException('Depth cannot be smaller than zero.');
        }

        $this->depth -= 1;
    }
}
