<?php
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

$className = 'JMS\Serializer\Tests\Fixtures\ObjectWithXmlKeyValuePairs';

$metadata = new ClassMetadata($className);

$pMetadata = new PropertyMetadata($className, 'array');
$pMetadata->xmlKeyValuePairs = true;
$metadata->addPropertyMetadata($pMetadata);

return $metadata;
