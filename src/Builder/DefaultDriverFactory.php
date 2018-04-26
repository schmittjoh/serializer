<?php

declare(strict_types=1);

namespace JMS\Serializer\Builder;

use Doctrine\Common\Annotations\Reader;
use JMS\Parser\AbstractParser;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Metadata\Driver\XmlDriver;
use JMS\Serializer\Metadata\Driver\YamlDriver;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use JMS\Serializer\TypeParser;
use Metadata\Driver\DriverChain;
use Metadata\Driver\FileLocator;

final class DefaultDriverFactory implements DriverFactoryInterface
{
    private $typeParser;
    /**
     * @var PropertyNamingStrategyInterface
     */
    private $propertyNamingStrategy;

    public function __construct(PropertyNamingStrategyInterface $propertyNamingStrategy, AbstractParser $typeParser = null)
    {
        $this->typeParser = $typeParser ?: new TypeParser();
        $this->propertyNamingStrategy = $propertyNamingStrategy;
    }

    public function createDriver(array $metadataDirs, Reader $annotationReader)
    {
        if (!empty($metadataDirs)) {
            $fileLocator = new FileLocator($metadataDirs);

            return new DriverChain(array(
                new YamlDriver($fileLocator, $this->propertyNamingStrategy, $this->typeParser),
                new XmlDriver($fileLocator, $this->propertyNamingStrategy, $this->typeParser),
                new AnnotationDriver($annotationReader, $this->propertyNamingStrategy, $this->typeParser),
            ));
        }

        return new AnnotationDriver($annotationReader, $this->propertyNamingStrategy, $this->typeParser);
    }
}
