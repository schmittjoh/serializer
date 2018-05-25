<?php

namespace JMS\Serializer\Tests\Fixtures\Discriminator;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\Discriminator(field = "type", map = {
 *    "child": "JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlAttributeDiscriminatorChild"
 * })
 * @Serializer\XmlDiscriminator(attribute=true, cdata=false)
 */
class ObjectWithXmlAttributeDiscriminatorParent
{

}