<?php

declare(strict_types=1);

namespace JMS\Serializer\Visitor\Factory;

use JMS\Serializer\JsonDeserializationStrictVisitor;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
final class JsonDeserializationVisitorFactory implements DeserializationVisitorFactory
{
    /**
     * @var int
     */
    private $options = 0;

    /**
     * @var int
     */
    private $depth = 512;

    /**
     * @var bool
     */
    private $strict;

    public function __construct(bool $strict = false)
    {
        $this->strict = $strict;
    }

    public function getVisitor(): DeserializationVisitorInterface
    {
        if ($this->strict) {
            return new JsonDeserializationStrictVisitor($this->options, $this->depth);
        }

        return new JsonDeserializationVisitor($this->options, $this->depth);
    }

    public function setOptions(int $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function setDepth(int $depth): self
    {
        $this->depth = $depth;

        return $this;
    }
}
