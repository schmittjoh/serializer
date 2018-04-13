<?php

namespace JMS\Serializer\Builder;

use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\Metadata\ClassMetadataUpdaterInterface;
use Metadata\Driver\DriverInterface;

interface DriverFactoryInterface
{
    /**
     * @param array $metadataDirs
     * @param Reader $annotationReader
     * @param ClassMetadataUpdaterInterface|null $propertyUpdater
     *
     * @return DriverInterface
     */
    public function createDriver(
        array $metadataDirs,
        Reader $annotationReader,
        ClassMetadataUpdaterInterface $propertyUpdater = null
    );
}
