<?php

use JMS\SerializerBundle\Metadata\ClassMetadata;
use JMS\SerializerBundle\Metadata\VirtualPropertyMetadata;

$className = 'JMS\SerializerBundle\Tests\Fixtures\ObjectWithVirtualPropertiesAndExcludeAll';

$metadata = new ClassMetadata( $className );

$pMetadata = new VirtualPropertyMetadata($className, 'virtualValue');
$pMetadata->getter = 'getVirtualValue';
$metadata->addPropertyMetadata($pMetadata);

return $metadata;