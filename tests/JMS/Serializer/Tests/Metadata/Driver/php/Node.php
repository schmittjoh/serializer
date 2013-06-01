<?php

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

$metadata = new ClassMetadata('JMS\Serializer\Tests\Fixtures\Node');

$pMetadata = new PropertyMetadata('JMS\Serializer\Tests\Fixtures\Node', 'children');
$pMetadata->maxDepth = 2;
$metadata->addPropertyMetadata($pMetadata);

return $metadata;
