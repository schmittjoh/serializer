<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class AccessorSetter
{
    /**
     * @Serializer\Type("JMS\Serializer\Tests\Fixtures\AccessorSetterElement")
     * @Serializer\Accessor(setter="setElementDifferent")
     *
     * @var \stdClass
     */
    protected $element;

    /**
     * @Serializer\Type("array<string>")
     * @Serializer\Accessor(setter="setCollectionDifferent")
     * @Serializer\XmlList(inline=false)
     *
     * @var array
     */
    protected $collection;

    /**
     * @return \stdClass
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param AccessorSetterElement $element
     */
    public function setElementDifferent(AccessorSetterElement $element)
    {
        $this->element = new \stdClass();
        $this->element->element = $element;
    }

    /**
     * @return array
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param array $collection
     */
    public function setCollectionDifferent($collection)
    {
        $this->collection = array_combine($collection, $collection);
    }
}

class AccessorSetterElement
{
    /**
     * @Serializer\Type("string")
     * @Serializer\Accessor(setter="setAttributeDifferent")
     * @Serializer\XmlAttribute
     *
     * @var string
     */
    protected $attribute;

    /**
     * @Serializer\Type("string")
     * @Serializer\Accessor(setter="setElementDifferent")
     * @Serializer\XmlValue
     *
     * @var string
     */
    protected $element;

    /**
     * @return string
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param string $attribute
     */
    public function setAttributeDifferent($attribute)
    {
        $this->attribute = $attribute . '-different';
    }

    /**
     * @param string $element
     */
    public function setElementDifferent($element)
    {
        $this->element = $element . '-different';
    }

    /**
     * @return string
     */
    public function getElement()
    {
        return $this->element;
    }
}
