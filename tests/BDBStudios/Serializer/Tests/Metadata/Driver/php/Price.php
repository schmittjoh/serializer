<?php

use BDBStudios\Serializer\Metadata\ClassMetadata;
use BDBStudios\Serializer\Metadata\PropertyMetadata;

$metadata = new ClassMetadata('BDBStudios\Serializer\Tests\Fixtures\Price');

$pMetadata = new PropertyMetadata('BDBStudios\Serializer\Tests\Fixtures\Price', 'price');
$pMetadata->setType('double');
$pMetadata->xmlValue = true;
$metadata->addPropertyMetadata($pMetadata);

return $metadata;
