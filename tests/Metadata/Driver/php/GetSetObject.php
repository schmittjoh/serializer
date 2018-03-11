<?php

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Tests\Fixtures\GetSetObject;

$propertyMetadata = new PropertyMetadata(GetSetObject::class, 'underscored_property');
$propertyMetadata->setType('string');

$classMetadata = new ClassMetadata(GetSetObject::class);
$classMetadata->addPropertyMetadata($propertyMetadata);
$classMetadata->accessType = PropertyMetadata::ACCESS_TYPE_PUBLIC_METHOD;
$classMetadata->accessTypeNaming = PropertyMetadata::ACCESS_TYPE_NAMING_CAMEL_CASE;
return $classMetadata;
