<?php

declare(strict_types=1);

namespace JMS\Serializer\Builder;

use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Metadata\Driver\XmlDriver;
use JMS\Serializer\Metadata\Driver\YamlDriver;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use JMS\Serializer\Type\Parser;
use JMS\Serializer\Type\ParserInterface;
use Metadata\Driver\DriverChain;
use Metadata\Driver\DriverInterface;
use Metadata\Driver\FileLocator;

final class DefaultDriverFactory implements DriverFactoryInterface
{
    /**
     * @var ParserInterface
     */
    private $typeParser;
    /**
     * @var PropertyNamingStrategyInterface
     */
    private $propertyNamingStrategy;

    public function __construct(PropertyNamingStrategyInterface $propertyNamingStrategy, ?ParserInterface $typeParser = null)
    {
        $this->typeParser = $typeParser ?: new Parser();
        $this->propertyNamingStrategy = $propertyNamingStrategy;
    }

    public function createDriver(array $metadataDirs, Reader $annotationReader): DriverInterface
    {
        if (!empty($metadataDirs)) {
            $fileLocator = new FileLocator($metadataDirs);

            return new DriverChain([
                new YamlDriver($fileLocator, $this->propertyNamingStrategy, $this->typeParser),
                new XmlDriver($fileLocator, $this->propertyNamingStrategy, $this->typeParser),
                new AnnotationDriver($annotationReader, $this->propertyNamingStrategy, $this->typeParser),
            ]);
        }

        return new AnnotationDriver($annotationReader, $this->propertyNamingStrategy, $this->typeParser);
    }
}
