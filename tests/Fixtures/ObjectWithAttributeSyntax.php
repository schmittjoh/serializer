<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("test-object")
 * @Serializer\XmlNamespace(uri="http://example.com/default", prefix="")
 * @Serializer\XmlNamespace(uri="http://example.com/ns1", prefix="ns1")
 */
class ObjectWithAttributeSyntax
{
    /**
     * @Serializer\SerializedName("Value")
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    public $value;

    /**
     * @Serializer\SerializedName("@Identifier")
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    public $testIdentifier;

    /**
     * @Serializer\SerializedName("@NamespacedIdentifier")
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false, namespace="http://example.com/ns1")
     */
    public $testIdentifierNs;

    /**
     * @Serializer\SerializedName("@NullableIdentifier")
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    public $nullableIdentifierValue = null;

    /**
     * @Serializer\SerializedName("@NamespacedNullableIdentifier")
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false, namespace="http://example.com/ns1")
     */
    public $nullableIdentifierScheme = null;

    public function __construct(
        string $value,
        string $testIdentifier,
        string $testIdentifierNs
    ) {
        $this->value = $value;
        $this->testIdentifier = $testIdentifier;
        $this->testIdentifierNs = $testIdentifierNs;
    }
}
