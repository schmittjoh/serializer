<?php

use BDBStudios\Serializer\Metadata\ClassMetadata;
use BDBStudios\Serializer\Metadata\PropertyMetadata;
use BDBStudios\Serializer\Metadata\VirtualPropertyMetadata;

$className = 'BDBStudios\Serializer\Tests\Fixtures\ObjectWithVirtualProperties';

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

$pMetadata = new VirtualPropertyMetadata($className, 'typedVirtualProperty');
$pMetadata->getter = 'getTypedVirtualProperty';
$pMetadata->setType('integer');
$metadata->addPropertyMetadata($pMetadata);

return $metadata;
