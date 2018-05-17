<?php

namespace JMS\Serializer\Tests\Fixtures;


use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot(name="object")
 */
class ObjectWithXmlListWithObjectType
{
    /**
     * @var array
     * @Serializer\Type(name="array<MS\Serializer\Tests\Fixtures\ObjectWithXmlListWithObjectTypesInterface>")
     * @ Serializer\XmlList(inline=true)
     * @Serializer\XmlList(inline=true, entry="item", allowTypes={
     *     @Serializer\XmlElementRef(name="TypeA", type="JMS\Serializer\Tests\Fixtures\ObjectWithXmlListWithObjectTypeA"),
     *     @Serializer\XmlElementRef(name="TypeB", type="JMS\Serializer\Tests\Fixtures\ObjectWithXmlListWithObjectTypeB")
     * })
     */
    private $list;

    public function __construct()
    {
        $this->list = self::create();
    }

    public static function create()
    {
        return
            [
                new ObjectWithXmlListWithObjectTypeA('testA'),
                new ObjectWithXmlListWithObjectTypeB(),
                new ObjectWithXmlListWithObjectTypeA(),
                new ObjectWithXmlListWithObjectTypeB('testB'),
            ]
        ;
    }
}
