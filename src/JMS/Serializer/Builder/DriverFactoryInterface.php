<?php

namespace JMS\Serializer\Builder;

use Doctrine\Common\Annotations\Reader;
use Metadata\Driver\DriverInterface;

interface DriverFactoryInterface
{
    /**
     * @param array $metadataDirs
     * @param Reader $annotationReader
     *
     * @return DriverInterface
     */
    public function createDriver(array $metadataDirs, Reader $annotationReader);
}