<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("AccessorSetter")
 */
class AccessorSetter 
{
    /**
     * @var int
     * @Serializer\Type("string")
     * @Serializer\Accessor(getter="getAttributeString",setter="setAttribute")
     * @Serializer\XmlAttribute
     */
    protected $attribute;

    /**
     * @var int
     * @Serializer\Type("string")
     * @Serializer\Accessor(getter="getElementString",setter="setElement")
     * @Serializer\XmlElement(cdata=false)
     * @Serializer\XmlValue
     */
    protected $element;


    /**
     * @return int
     */
    public function getAttributeTimestamp()
    {
        return $this->attribute;
    }

    /**
     * @return string
     */
    public function getAttributeString()
    {
        return date('Y-m-d H:i:s', $this->attribute);
    }

    /**
     * @param int|string|\DateTime $value
     * @throws \InvalidArgumentException
     */
    public function setAttribute($value)
    {
        if (is_int($value)) {
            $this->attribute = $value;
        } else if (is_string($value)) {
            $this->attribute = strtotime($value);
        } else if ($value instanceof \DateTime) {
            $this->attribute = $value->getTimestamp();
        } else {
            throw new \InvalidArgumentException();
        }
    }


    /**
     * @return int
     */
    public function getElementTimestamp()
    {
        return $this->element;
    }

    /**
     * @return string
     */
    public function getElementString()
    {
        return date('Y-m-d H:i:s', $this->element);
    }

    /**
     * @param int|string|\DateTime $value
     * @throws \InvalidArgumentException
     */
    public function setElement($value)
    {
        if (is_int($value)) {
            $this->element = $value;
        } else if (is_string($value)) {
            $this->element = strtotime($value);
        } else if ($value instanceof \DateTime) {
            $this->element = $value->getTimestamp();
        } else {
            throw new \InvalidArgumentException();
        }
    }

} 