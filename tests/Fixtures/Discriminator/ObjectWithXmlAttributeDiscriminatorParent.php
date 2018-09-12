<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Discriminator;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\Discriminator(field = "type", map = {
 *    "child": "JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlAttributeDiscriminatorChild"
 * })
 * @Serializer\XmlDiscriminator(attribute=true, cdata=false)
 */
abstract class ObjectWithXmlAttributeDiscriminatorParent
{
}
