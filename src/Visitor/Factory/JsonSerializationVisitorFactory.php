<?php

declare(strict_types=1);

namespace JMS\Serializer\Visitor\Factory;

use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use const JSON_PRESERVE_ZERO_FRACTION;

final class JsonSerializationVisitorFactory implements SerializationVisitorFactory
{
    /** @var int */
    private $options = JSON_PRESERVE_ZERO_FRACTION;

    public function getVisitor(): SerializationVisitorInterface
    {
        return new JsonSerializationVisitor($this->options);
    }

    public function setOptions(int $options): self
    {
        $this->options = (int) $options;
        return $this;
    }
}
