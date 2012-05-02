<?php

use JMS\SerializerBundle\Metadata\ClassMetadata;
use JMS\SerializerBundle\Metadata\PropertyMetadata;
use JMS\SerializerBundle\Metadata\VirtualPropertyMetadata;

$className = 'JMS\SerializerBundle\Tests\Fixtures\ObjectWithVirtualProperties';

$metadata = new ClassMetadata( $className );

$pMetadata = new PropertyMetadata($className, 'existField');
$metadata->addPropertyMetadata($pMetadata);

$pMetadata = new VirtualPropertyMetadata($className, 'foo');
$pMetadata->getter = 'getVirtualValue';
$metadata->addPropertyMetadata($pMetadata);


$pMetadata = new VirtualPropertyMetadata($className, 'prop_name');
$pMetadata->getter = 'getVirtualSerializedValue';
$pMetadata->serializedName = 'test';
$metadata->addPropertyMetadata($pMetadata);

return $metadata;