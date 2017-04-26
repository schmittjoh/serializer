<?php

use JMS\Serializer\Metadata\ClassMetadata;

$metadata = new ClassMetadata('JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlNamespaceDiscriminatorParent');
$metadata->setDiscriminator('type', array(
    'child' => 'JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlNamespaceDiscriminatorChild'
));
$metadata->xmlDiscriminatorNamespace = 'http://example.com/';

return $metadata;
