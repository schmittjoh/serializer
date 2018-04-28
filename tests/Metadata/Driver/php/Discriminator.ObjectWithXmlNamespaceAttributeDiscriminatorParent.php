<?php

use JMS\Serializer\Metadata\ClassMetadata;

$metadata = new ClassMetadata('JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlNamespaceAttributeDiscriminatorParent');
$metadata->setDiscriminator('type', array(
    'child' => 'JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlNamespaceAttributeDiscriminatorChild'
));
$metadata->xmlDiscriminatorAttribute = true;
$metadata->xmlDiscriminatorNamespace = 'http://example.com/';

return $metadata;
