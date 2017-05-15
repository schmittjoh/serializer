<?php

use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

$metadata = new ClassMetadata('JMS\Serializer\Tests\Fixtures\ObjectWithHandlerCallbacks');

$pMetadata = new PropertyMetadata('JMS\Serializer\Tests\Fixtures\ObjectWithHandlerCallbacks', 'name');
$pMetadata->type = 'string';
$metadata->addPropertyMetadata($pMetadata);

$metadata->addHandlerCallback(GraphNavigator::DIRECTION_SERIALIZATION, 'json', 'toJson');
$metadata->addHandlerCallback(GraphNavigator::DIRECTION_SERIALIZATION, 'xml', 'toXml');

return $metadata;
