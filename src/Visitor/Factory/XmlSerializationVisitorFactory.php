<?php

declare(strict_types=1);

namespace JMS\Serializer\Visitor\Factory;

use JMS\Serializer\Visitor\SerializationVisitorInterface;
use JMS\Serializer\XmlSerializationVisitor;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
final class XmlSerializationVisitorFactory implements SerializationVisitorFactory
{
    /**
     * @var string
     */
    private $defaultRootName = 'result';

    /**
     * @var string
     */
    private $defaultVersion = '1.0';

    /**
     * @var string
     */
    private $defaultEncoding = 'UTF-8';

    /**
     * @var bool
     */
    private $formatOutput = true;

    /**
     * @var string|null
     */
    private $defaultRootNamespace;

    public function getVisitor(): SerializationVisitorInterface
    {
        return new XmlSerializationVisitor(
            $this->formatOutput,
            $this->defaultEncoding,
            $this->defaultVersion,
            $this->defaultRootName,
            $this->defaultRootNamespace,
        );
    }

    public function setDefaultRootName(string $name, ?string $namespace = null): self
    {
        $this->defaultRootName = $name;
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
