<?php

namespace JMS\Serializer\Metadata\Driver;

use JMS\Serializer\Metadata\ClassMetadata;
use Metadata\Driver\DriverInterface;

class NullDriver implements DriverInterface
{
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $classMetadata = new ClassMetadata($name = $class->name);
        $classMetadata->fileResources[] = $class->getFilename();

        return $classMetadata;
    }
}
