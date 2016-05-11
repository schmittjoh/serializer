<?php

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;

$className = 'JMS\Serializer\Tests\Fixtures\ObjectWithVirtualProperties';

$metadata = new ClassMetadata( $className );

$pMetadata = new PropertyMetadata($className, 'realField');
$metadata->addPropertyMetadata($pMetadata);

$pMetadata = new VirtualPropertyMetadata($className, 'virtualField');
$pMetadata->getter = 'getVirtualField';
$metadata->addPropertyMetadata($pMetadata);

$pMetadata = new VirtualPropertyMetadata($className, 'virtualFieldToBeRenamed');
$pMetadata->getter = 'getVirtualFieldToBeRenamed';
$pMetadata->serializedName = 'renamed_virtual_field';
$metadata->addPropertyMetadata($pMetadata);

$pMetadata = new VirtualPropertyMetadata($className, 'typedVirtualField');
$pMetadata->getter = 'getTypedVirtualField';
$pMetadata->setType('integer');
$metadata->addPropertyMetadata($pMetadata);

return $metadata;
