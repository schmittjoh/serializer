<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\AccessorOrder;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\SkipWhenEmpty;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * @AccessorOrder("custom", custom = {"prop_name", "existField", "foo" })
 */
#[AccessorOrder(order: 'custom', custom: ['prop_name', 'existField', 'foo'])]
class ObjectWithVirtualProperties
{
    /**
     * @Type("string")
     */
    #[Type(name: 'string')]
    protected $existField = 'value';

    /**
     * @VirtualProperty
     */
    #[VirtualProperty]
    public function getVirtualValue()
    {
        return 'value';
    }

    /**
     * @VirtualProperty
     * @SerializedName("test")
     */
    #[VirtualProperty]
    #[SerializedName(name: 'test')]
    public function getVirtualSerializedValue()
    {
        return 'other-name';
    }

    /**
     * @VirtualProperty
     * @Type("integer")
     */
    #[VirtualProperty]
    #[Type(name: 'integer')]
    public function getTypedVirtualProperty()
    {
        return '1';
    }

    /**
     * @VirtualProperty
     * @SkipWhenEmpty()
     */
    #[VirtualProperty]
    #[SkipWhenEmpty]
    public function getEmptyArray()
    {
        return [];
    }
}
