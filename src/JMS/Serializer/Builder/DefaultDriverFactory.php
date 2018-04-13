<?php

namespace JMS\Serializer\Builder;

use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Metadata\Driver\XmlDriver;
use JMS\Serializer\Metadata\Driver\YamlDriver;
use JMS\Serializer\Metadata\ClassMetadataUpdaterInterface;
use Metadata\Driver\DriverChain;
use Metadata\Driver\FileLocator;

class DefaultDriverFactory implements DriverFactoryInterface
{
    public function createDriver(
        array $metadataDirs,
        Reader $annotationReader,
        ClassMetadataUpdaterInterface $propertyUpdater = null
    )
    {
        $annotationDriver = new AnnotationDriver($annotationReader, $propertyUpdater);
        if (empty($metadataDirs)) {
            return $annotationDriver;
        }

        $fileLocator = new FileLocator($metadataDirs);

        return new DriverChain(array(
            new YamlDriver($fileLocator, $propertyUpdater),
            new XmlDriver($fileLocator, $propertyUpdater),
            $annotationDriver,
        ));
    }
}
