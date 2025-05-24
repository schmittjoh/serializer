<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("test-object")
 * @Serializer\XmlNamespace(uri="http://example.com/default", prefix="")
 * @Serializer\XmlNamespace(uri="http://example.com/ns1", prefix="ns1")
 */
class ObjectWithElementAttributeSyntax
{
    /**
     * @Serializer\SerializedName("Identifier")
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    public $identifierValue;

    /**
     * @Serializer\SerializedName("Identifier/@scheme")
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    public $identifierScheme;

    /**
     * @Serializer\SerializedName("Description")
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    public $descriptionValue = null;

    /**
     * @Serializer\SerializedName("Description/@lang")
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    public $descriptionLang = null;

    /**
     * @Serializer\SerializedName("CustomElement")
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false, namespace="http://example.com/ns1")
     */
    public $customElementValue;

    /**
     * @Serializer\SerializedName("CustomElement/@type")
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false, namespace="http://example.com/ns1")
     */
    public $customElementType;

    /**
     * @Serializer\SerializedName("DataPoint")
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    public $dataPointValue;

    /**
     * @Serializer\SerializedName("DataPoint/@unit")
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    public $dataPointUnit = null;

    /**
     * @Serializer\SerializedName("NullableIdentifier")
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    public $nullableIdentifierValue = null;

    /**
     * @Serializer\SerializedName("NullableIdentifier/@scheme")
     * @Serializer\Type("string")
     * @Serializer\XmlElement(cdata=false)
     */
    public $nullableIdentifierScheme = null;

    public function __construct(
        string $identifierValue,
        string $identifierScheme,
        string $customElementValue,
        string $customElementType,
        string $dataPointValue
    ) {
        $this->identifierValue = $identifierValue;
        $this->identifierScheme = $identifierScheme;
        $this->customElementValue = $customElementValue;
        $this->customElementType = $customElementType;
        $this->dataPointValue = $dataPointValue;
    }
}
