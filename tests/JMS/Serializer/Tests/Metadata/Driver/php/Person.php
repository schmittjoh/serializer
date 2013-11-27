<?php

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

$metadata = new ClassMetadata('JMS\Serializer\Tests\Fixtures\Person');
$metadata->xmlRootName = 'child';

$pMetadata = new PropertyMetadata('JMS\Serializer\Tests\Fixtures\Person', 'name');
$pMetadata->setType('string');
$pMetadata->xmlValue = true;
$pMetadata->xmlElementCData = false;
$metadata->addPropertyMetadata($pMetadata);

$pMetadata = new PropertyMetadata('JMS\Serializer\Tests\Fixtures\Person', 'age');
$pMetadata->setType('integer');
$pMetadata->xmlAttribute = true;
$metadata->addPropertyMetadata($pMetadata);

return $metadata;
