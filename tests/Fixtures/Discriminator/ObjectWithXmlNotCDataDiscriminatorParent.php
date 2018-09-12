<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Discriminator;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\Discriminator(field = "type", map = {
 *    "child": "JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlNotCDataDiscriminatorChild"
 * })
 * @Serializer\XmlDiscriminator(cdata=false)
 */
abstract class ObjectWithXmlNotCDataDiscriminatorParent
{
}
