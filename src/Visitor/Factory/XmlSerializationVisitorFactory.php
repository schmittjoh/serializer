<?php

declare(strict_types=1);

namespace JMS\Serializer\Visitor\Factory;

use JMS\Serializer\Visitor\SerializationVisitorInterface;
use JMS\Serializer\XmlSerializationVisitor;

final class XmlSerializationVisitorFactory implements SerializationVisitorFactory
{
    private $defaultRootName = 'result';
    private $defaultVersion  = '1.0';
    private $defaultEncoding = 'UTF-8';
    private $formatOutput    = true;
    private $defaultRootNamespace;

    public function getVisitor(): SerializationVisitorInterface
    {
        return new XmlSerializationVisitor(
            $this->formatOutput,
            $this->defaultEncoding,
            $this->defaultVersion,
            $this->defaultRootName,
            $this->defaultRootNamespace
        );
    }

    public function setDefaultRootName(string $name, ?string $namespace = null): self
    {
        $this->defaultRootName      = $name;
        $this->defaultRootNamespace = $namespace;
        return $this;
    }

    public function setDefaultVersion(string $version): self
    {
        $this->defaultVersion = $version;
        return $this;
    }

    public function setDefaultEncoding(string $encoding): self
    {
        $this->defaultEncoding = $encoding;
        return $this;
    }

    public function setFormatOutput(bool $formatOutput): self
    {
        $this->formatOutput = (bool) $formatOutput;
        return $this;
    }
}
