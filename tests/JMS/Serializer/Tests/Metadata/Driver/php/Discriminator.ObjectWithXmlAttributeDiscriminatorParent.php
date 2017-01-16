<?php

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

$metadata = new ClassMetadata('JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlAttributeDiscriminatorParent');
$metadata->setDiscriminator('type', array(
    'child' => 'JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlAttributeDiscriminatorChild'
));
$metadata->xmlDiscriminatorAttribute = true;
$metadata->xmlDiscriminatorCData = false;
return $metadata;
