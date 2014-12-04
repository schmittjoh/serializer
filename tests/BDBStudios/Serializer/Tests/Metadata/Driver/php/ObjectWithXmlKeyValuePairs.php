<?php
use BDBStudios\Serializer\Metadata\ClassMetadata;
use BDBStudios\Serializer\Metadata\PropertyMetadata;

$className = 'BDBStudios\Serializer\Tests\Fixtures\ObjectWithXmlKeyValuePairs';

$metadata = new ClassMetadata($className);

$pMetadata = new PropertyMetadata($className, 'array');
$pMetadata->xmlKeyValuePairs = true;
$metadata->addPropertyMetadata($pMetadata);

return $metadata;
