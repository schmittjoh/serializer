<?php
use JMS\SerializerBundle\Metadata\ClassMetadata;
use JMS\SerializerBundle\Metadata\PropertyMetadata;

$className = 'JMS\SerializerBundle\Tests\Fixtures\ObjectWithXmlKeyValuePairs';

$metadata = new ClassMetadata($className);

$pMetadata = new PropertyMetadata($className, 'array');
$pMetadata->xmlKeyValuePairs = true;
$metadata->addPropertyMetadata($pMetadata);

return $metadata;
