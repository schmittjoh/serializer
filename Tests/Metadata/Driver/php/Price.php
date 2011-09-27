<?php

use JMS\SerializerBundle\Metadata\ClassMetadata;
use JMS\SerializerBundle\Metadata\PropertyMetadata;

$metadata = new ClassMetadata('JMS\SerializerBundle\Tests\Fixtures\Price');

$pMetadata = new PropertyMetadata('JMS\SerializerBundle\Tests\Fixtures\Price', 'price');
$pMetadata->type = 'double';
$pMetadata->xmlValue = true;
$metadata->addPropertyMetadata($pMetadata);

return $metadata;