<?php

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

$metadata = new ClassMetadata('JMS\Serializer\Tests\Fixtures\Discriminator\Vehicle');
$metadata->setDiscriminator('type', array(
    'car' => 'JMS\Serializer\Tests\Fixtures\Discriminator\Car',
    'moped' => 'JMS\Serializer\Tests\Fixtures\Discriminator\Moped',
));

$km = new PropertyMetadata('JMS\Serializer\Tests\Fixtures\Discriminator\Vehicle', 'km');
$km->setType('integer');
$metadata->addPropertyMetadata($km);

return $metadata;