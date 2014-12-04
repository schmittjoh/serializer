<?php

use BDBStudios\Serializer\Metadata\ClassMetadata;
use BDBStudios\Serializer\Metadata\VirtualPropertyMetadata;

$className = 'BDBStudios\Serializer\Tests\Fixtures\ObjectWithVirtualPropertiesAndExcludeAll';

$metadata = new ClassMetadata( $className );

$pMetadata = new VirtualPropertyMetadata($className, 'virtualValue');
$pMetadata->getter = 'getVirtualValue';
$metadata->addPropertyMetadata($pMetadata);

return $metadata;
