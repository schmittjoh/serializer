<?php

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

$metadata = new ClassMetadata('JMS\Serializer\Tests\Fixtures\Discriminator\Post');
$metadata->setDiscriminator('type', array(
    'post' => 'JMS\Serializer\Tests\Fixtures\Discriminator\Post',
    'image_post' => 'JMS\Serializer\Tests\Fixtures\Discriminator\ImagePost',
));

$title = new PropertyMetadata('JMS\Serializer\Tests\Fixtures\Discriminator\Post', 'title');
$title->setType('string');
$metadata->addPropertyMetadata($title);

return $metadata;
