<?php

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;

$className = 'JMS\Serializer\Tests\Fixtures\ObjectWithAbsentXmlListNode';

$metadata = new ClassMetadata( $className );

$pMetadata = new PropertyMetadata($className, 'absent');
$pMetadata->xmlCollectionSkipWhenEmpty = true;
$metadata->addPropertyMetadata($pMetadata);

$pMetadata = new PropertyMetadata($className, 'present');
$pMetadata->xmlCollectionSkipWhenEmpty = false;
$metadata->addPropertyMetadata($pMetadata);

$pMetadata = new PropertyMetadata($className, 'skipDefault');
$metadata->addPropertyMetadata($pMetadata);


return $metadata;
