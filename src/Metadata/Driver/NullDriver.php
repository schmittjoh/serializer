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
        $fileResource =  $class->getFilename();
        if (false !== $fileResource) {
            $classMetadata->fileResources[] = $fileResource;
        }

        return $classMetadata;
    }
}
