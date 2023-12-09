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
    #[Serializer\Type(name: 'JMS\Serializer\Tests\Fixtures\AccessorSetterElement')]
    #[Serializer\Accessor(setter: 'setElementDifferent')]
    protected $element;

    /**
     * @Serializer\Type("array<string>")
     * @Serializer\Accessor(setter="setCollectionDifferent")
     * @Serializer\XmlList(inline=false)
     *
     * @var array
     */
    #[Serializer\Type(name: 'array<string>')]
    #[Serializer\Accessor(setter: 'setCollectionDifferent')]
    #[Serializer\XmlList(inline: false)]
    protected $collection;

    /**
     * @return \stdClass
     */
    public function getElement()
    {
        return $this->element;
    }

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
