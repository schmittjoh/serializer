<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class AccessorSetterElement
{
    /**
     * @Serializer\Type("string")
     * @Serializer\Accessor(setter="setAttributeDifferent")
     * @Serializer\XmlAttribute
     *
     * @var string
     */
    #[Serializer\Type(name: 'string')]
    #[Serializer\Accessor(setter: 'setAttributeDifferent')]
    #[Serializer\XmlAttribute]
    protected $attribute;

    /**
     * @Serializer\Type("string")
     * @Serializer\Accessor(setter="setElementDifferent")
     * @Serializer\XmlValue
     *
     * @var string
     */
    #[Serializer\Type(name: 'string')]
    #[Serializer\Accessor(setter: 'setElementDifferent')]
    #[Serializer\XmlValue]
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
