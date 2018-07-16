<?php

declare(strict_types=1);

namespace JMS\Serializer\Metadata\Driver;

use JMS\Serializer\Metadata\ClassMetadata;
use Metadata\ClassMetadata as BaseClassMetadata;
use Metadata\Driver\DriverInterface;

class NullDriver implements DriverInterface
{
    public function loadMetadataForClass(\ReflectionClass $class): ?BaseClassMetadata
    {
        $classMetadata = new ClassMetadata($name = $class->name);
        $classMetadata->fileResources[] = $class->getFilename();

        return $classMetadata;
    }
}
