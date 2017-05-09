<?php

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

$metadata = new ClassMetadata('JMS\Serializer\Tests\Fixtures\Discriminator\AbstractUser');
$metadata->setDiscriminator('type', array(
    'user' => 'JMS\Serializer\Tests\Fixtures\Discriminator\User',
    'contact' => 'JMS\Serializer\Tests\Fixtures\Discriminator\Contact',
), array(), false);

$name = new PropertyMetadata('JMS\Serializer\Tests\Fixtures\Discriminator\AbstractUser', 'name');
$name->setType('string');
$metadata->addPropertyMetadata($name);

return $metadata;
