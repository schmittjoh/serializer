<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("test-object")
 */
class ObjectWithAttributeSyntaxWithoutNs
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
     * @Serializer\SerializedName("@NullableIdentifier")
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    public $nullableIdentifierValue = null;

    /**
     * @Serializer\SerializedName("@NullableIdentifierScheme")
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    public $nullableIdentifierScheme = null;

    public function __construct(
        string $value,
        string $testIdentifier
    ) {
        $this->value = $value;
        $this->testIdentifier = $testIdentifier;
    }
}
