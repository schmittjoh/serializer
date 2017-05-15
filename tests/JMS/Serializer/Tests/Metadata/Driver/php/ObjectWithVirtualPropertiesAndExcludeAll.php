<?php

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;

$className = 'JMS\Serializer\Tests\Fixtures\ObjectWithVirtualPropertiesAndExcludeAll';

$metadata = new ClassMetadata($className);

$pMetadata = new VirtualPropertyMetadata($className, 'virtualValue');
$pMetadata->getter = 'getVirtualValue';
$metadata->addPropertyMetadata($pMetadata);

return $metadata;
