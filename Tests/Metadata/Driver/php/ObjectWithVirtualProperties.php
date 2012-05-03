<?php

use JMS\SerializerBundle\Metadata\ClassMetadata;
use JMS\SerializerBundle\Metadata\PropertyMetadata;
use JMS\SerializerBundle\Metadata\VirtualPropertyMetadata;

$className = 'JMS\SerializerBundle\Tests\Fixtures\ObjectWithVirtualProperties';

$metadata = new ClassMetadata( $className );

$pMetadata = new PropertyMetadata($className, 'existField');
$metadata->addPropertyMetadata($pMetadata);

$pMetadata = new VirtualPropertyMetadata($className, 'virtualValue');
$pMetadata->getter = 'getVirtualValue';
$metadata->addPropertyMetadata($pMetadata);


$pMetadata = new VirtualPropertyMetadata($className, 'virtualSerializedValue');
$pMetadata->getter = 'getVirtualSerializedValue';
$pMetadata->serializedName = 'test';
$metadata->addPropertyMetadata($pMetadata);

return $metadata;