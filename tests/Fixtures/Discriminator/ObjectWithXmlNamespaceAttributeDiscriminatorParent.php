<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Discriminator;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\Discriminator(field = "type", map = {
 *    "child": "JMS\Serializer\Tests\Fixtures\Discriminator\ObjectWithXmlNamespaceAttributeDiscriminatorChild"
 * })
 * @Serializer\XmlDiscriminator(namespace="http://example.com/", attribute=true, cdata=false)
 * @Serializer\XmlNamespace(prefix="foo", uri="http://example.com/")
 */
abstract class ObjectWithXmlNamespaceAttributeDiscriminatorParent
{
}
