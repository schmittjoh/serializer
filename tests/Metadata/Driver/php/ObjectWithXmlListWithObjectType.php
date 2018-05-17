<?php
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

$className = 'JMS\Serializer\Tests\Fixtures\ObjectWithXmlListWithObjectType';

$metadata = new ClassMetadata($className);

$pMetadata = new PropertyMetadata($className, 'list');
$pMetadata->xmlAllowTypes = array(
    array(
        'type' => 'JMS\Serializer\Tests\Fixtures\ObjectWithXmlListWithObjectTypeA',
        'name' => 'TypeA',
        'namespace' => null
    ),
    array(
        'type' => 'JMS\Serializer\Tests\Fixtures\ObjectWithXmlListWithObjectTypeB',
        'name' => 'TypeB',
        'namespace' => null
    )
);
$metadata->addPropertyMetadata($pMetadata);

return $metadata;