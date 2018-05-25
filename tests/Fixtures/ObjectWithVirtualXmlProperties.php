<?php

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
     *
     * @VirtualProperty
     * @SerializedName("foo")
     * @Groups({"attributes"})
     * @XmlAttribute
     */
    public function getVirualXmlAttributeValue()
    {
        return 'bar';
    }

    /**
     *
     * @VirtualProperty
     * @SerializedName("xml-value")
     * @Groups({"values"})
     * @XmlValue
     */
    public function getVirualXmlValue()
    {
        return 'xml-value';
    }

    /**
     *
     * @VirtualProperty
     * @SerializedName("list")
     * @Groups({"list"})
     * @XmlList(inline = true, entry = "val")
     */
    public function getVirualXmlList()
    {
        return array('One', 'Two');
    }

    /**
     *
     * @VirtualProperty
     * @SerializedName("map")
     * @Groups({"map"})
     * @XmlMap(keyAttribute = "key")
     */
    public function getVirualXmlMap()
    {
        return array(
            'key-one' => 'One',
            'key-two' => 'Two'
        );
    }

    /**
     *
     * @VirtualProperty
     * @SerializedName("low")
     * @Groups({"versions"})
     * @Until("8")
     */
    public function getVirualLowValue()
    {
        return 1;
    }

    /**
     * @VirtualProperty
     * @SerializedName("hight")
     * @Groups({"versions"})
     * @Since("8")
     */
    public function getVirualHighValue()
    {
        return 8;
    }

}
