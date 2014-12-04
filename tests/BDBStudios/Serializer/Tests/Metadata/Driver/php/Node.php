<?php

use BDBStudios\Serializer\Metadata\ClassMetadata;
use BDBStudios\Serializer\Metadata\PropertyMetadata;

$metadata = new ClassMetadata('BDBStudios\Serializer\Tests\Fixtures\Node');

$pMetadata = new PropertyMetadata('BDBStudios\Serializer\Tests\Fixtures\Node', 'children');
$pMetadata->maxDepth = 2;
$metadata->addPropertyMetadata($pMetadata);

return $metadata;
