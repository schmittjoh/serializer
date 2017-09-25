<?php

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

$metadata = new ClassMetadata('JMS\Serializer\Tests\Fixtures\ParentSkipWithEmptyChild');

$pMeta = new PropertyMetadata($metadata->name, 'c');
$metadata->addPropertyMetadata($pMeta);

$pMeta = new PropertyMetadata($metadata->name, 'd');
$metadata->addPropertyMetadata($pMeta);

$pMeta = new PropertyMetadata($metadata->name, 'child');
$pMeta->skipWhenEmpty = true;

$metadata->addPropertyMetadata($pMeta);

return $metadata;
