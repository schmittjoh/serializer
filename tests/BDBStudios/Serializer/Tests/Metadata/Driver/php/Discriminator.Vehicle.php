<?php

use BDBStudios\Serializer\Metadata\ClassMetadata;
use BDBStudios\Serializer\Metadata\PropertyMetadata;

$metadata = new ClassMetadata('BDBStudios\Serializer\Tests\Fixtures\Discriminator\Vehicle');
$metadata->setDiscriminator('type', array(
    'car' => 'BDBStudios\Serializer\Tests\Fixtures\Discriminator\Car',
    'moped' => 'BDBStudios\Serializer\Tests\Fixtures\Discriminator\Moped',
));

$km = new PropertyMetadata('BDBStudios\Serializer\Tests\Fixtures\Discriminator\Vehicle', 'km');
$km->setType('integer');
$metadata->addPropertyMetadata($km);

return $metadata;
