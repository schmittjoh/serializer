<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\AccessorOrder;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Annotation\SkipWhenEmpty;

/**
 * @AccessorOrder("custom", custom = {"prop_name", "existField", "foo" })
 */
class ObjectWithVirtualProperties
{

    /**
     * @Type("string")
     */
    protected $existField = 'value';

    /**
     *
     * @VirtualProperty
     */
    public function getVirtualValue()
    {
        return 'value';
    }

    /**
     * @VirtualProperty
     * @SerializedName("test")
     */
    public function getVirtualSerializedValue()
    {
        return 'other-name';
    }

    /**
     * @VirtualProperty
     * @Type("integer")
     */
    public function getTypedVirtualProperty()
    {
        return '1';
    }

    /**
     * @VirtualProperty
     * @SkipWhenEmpty()
     */
    public function getEmptyArray()
    {
        return [];
    }
}
