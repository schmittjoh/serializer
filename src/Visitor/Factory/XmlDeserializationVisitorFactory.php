<?php

declare(strict_types=1);

namespace JMS\Serializer\Visitor\Factory;

use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\XmlDeserializationVisitor;

final class XmlDeserializationVisitorFactory implements DeserializationVisitorFactory
{
    private $disableExternalEntities = true;
    private $doctypeWhitelist        = [];

    public function getVisitor(): DeserializationVisitorInterface
    {
        return new XmlDeserializationVisitor($this->disableExternalEntities, $this->doctypeWhitelist);
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
}
