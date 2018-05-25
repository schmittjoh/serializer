<?php

namespace JMS\Serializer\Tests\Fixtures\Discriminator;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\Discriminator(field = "type", map = {
 *    "child": "JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlNotCDataDiscriminatorChild"
 * })
 * @Serializer\XmlDiscriminator(cdata=false)
 */
class ObjectWithXmlNotCDataDiscriminatorParent
{

}
