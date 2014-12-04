<?php

use BDBStudios\Serializer\Metadata\ClassMetadata;
use BDBStudios\Serializer\Metadata\PropertyMetadata;

$metadata = new ClassMetadata('BDBStudios\Serializer\Tests\Fixtures\Person');
$metadata->xmlRootName = 'child';

$pMetadata = new PropertyMetadata('BDBStudios\Serializer\Tests\Fixtures\Person', 'name');
$pMetadata->setType('string');
$pMetadata->xmlValue = true;
$pMetadata->xmlElementCData = false;
$metadata->addPropertyMetadata($pMetadata);

$pMetadata = new PropertyMetadata('BDBStudios\Serializer\Tests\Fixtures\Person', 'age');
$pMetadata->setType('integer');
$pMetadata->xmlAttribute = true;
$metadata->addPropertyMetadata($pMetadata);

return $metadata;
