<?php

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

$metadata = new ClassMetadata('JMS\Serializer\Tests\Fixtures\Price');

$pMetadata = new PropertyMetadata('JMS\Serializer\Tests\Fixtures\Price', 'price');
$pMetadata->setType('double');
$pMetadata->xmlValue = true;
$metadata->addPropertyMetadata($pMetadata);

return $metadata;
