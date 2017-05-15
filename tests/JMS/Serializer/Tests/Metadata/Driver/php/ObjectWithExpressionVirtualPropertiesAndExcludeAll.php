<?php

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\ExpressionPropertyMetadata;

$className = 'JMS\Serializer\Tests\Fixtures\ObjectWithExpressionVirtualPropertiesAndExcludeAll';

$metadata = new ClassMetadata($className);

$pMetadata = new ExpressionPropertyMetadata($className, 'virtualValue', 'object.getVirtualValue()');
$metadata->addPropertyMetadata($pMetadata);

return $metadata;
