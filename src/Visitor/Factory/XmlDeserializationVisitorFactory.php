<?php

declare(strict_types=1);

namespace JMS\Serializer\Visitor\Factory;

use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\XmlDeserializationVisitor;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
final class XmlDeserializationVisitorFactory implements DeserializationVisitorFactory
{
    /**
     * @var bool
     */
    private $disableExternalEntities = true;

    /**
     * @var string[]
     */
    private $doctypeWhitelist = [];

    /**
     * @var int
     */
    private $options = 0;

    public function getVisitor(): DeserializationVisitorInterface
    {
        return new XmlDeserializationVisitor($this->disableExternalEntities, $this->doctypeWhitelist, $this->options);
    }

    public function enableExternalEntities(bool $enable = true): self
    {
        $this->disableExternalEntities = !$enable;
        return $this;
    }

    /**
     * @param string[] $doctypeWhitelist
     */
    public function setDoctypeWhitelist(array $doctypeWhitelist): self
    {
        $this->doctypeWhitelist = $doctypeWhitelist;
        return $this;
    }

    public function setOptions(int $options): self
    {
        $this->options = $options;
        return $this;
    }
}
