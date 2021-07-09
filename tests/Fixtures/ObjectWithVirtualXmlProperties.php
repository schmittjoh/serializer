<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Until;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlMap;
use JMS\Serializer\Annotation\XmlValue;

class ObjectWithVirtualXmlProperties
{
    /**
     * @VirtualProperty
     * @SerializedName("foo")
     * @Groups({"attributes"})
     * @XmlAttribute
     */
    #[VirtualProperty]
    #[SerializedName(name: 'foo')]
    #[Groups(groups: ['attributes'])]
    #[XmlAttribute]
    public function getVirtualXmlAttributeValue()
    {
        return 'bar';
    }

    /**
     * @VirtualProperty
     * @SerializedName("xml-value")
     * @Groups({"values"})
     * @XmlValue
     */
    #[VirtualProperty]
    #[SerializedName(name: 'xml-value')]
    #[Groups(groups: ['values'])]
    #[XmlValue]
    public function getVirtualXmlValue()
    {
        return 'xml-value';
    }

    /**
     * @VirtualProperty
     * @SerializedName("list")
     * @Groups({"list"})
     * @XmlList(inline = true, entry = "val")
     */
    #[VirtualProperty]
    #[SerializedName(name: 'list')]
    #[Groups(groups: ['list'])]
    #[XmlList(entry: 'val', inline: true)]
    public function getVirtualXmlList()
    {
        return ['One', 'Two'];
    }

    /**
     * @VirtualProperty
     * @SerializedName("map")
     * @Groups({"map"})
     * @XmlMap(keyAttribute = "key")
     */
    #[VirtualProperty]
    #[SerializedName(name: 'map')]
    #[Groups(groups: ['map'])]
    #[XmlMap(keyAttribute: 'key')]
    public function getVirtualXmlMap()
    {
        return [
            'key-one' => 'One',
            'key-two' => 'Two',
        ];
    }

    /**
     * @VirtualProperty
     * @SerializedName("low")
     * @Groups({"versions"})
     * @Until("8")
     */
    #[VirtualProperty]
    #[SerializedName(name: 'low')]
    #[Groups(groups: ['versions'])]
    #[Until(version: '8')]
    public function getVirtualLowValue()
    {
        return 1;
    }

    /**
     * @VirtualProperty
     * @SerializedName("hight")
     * @Groups({"versions"})
     * @Since("8")
     */
    #[VirtualProperty]
    #[SerializedName(name: 'hight')]
    #[Groups(groups: ['versions'])]
    #[Since(version: '8')]
    public function getVirtualHighValue()
    {
        return 8;
    }
}
