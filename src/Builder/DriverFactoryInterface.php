<?php

declare(strict_types=1);

namespace JMS\Serializer\Builder;

use Doctrine\Common\Annotations\Reader;
use Metadata\Driver\DriverInterface;

interface DriverFactoryInterface
{
    /**
     * @param array $metadataDirs
     *
     */
    public function createDriver(array $metadataDirs, Reader $annotationReader): DriverInterface;
}
