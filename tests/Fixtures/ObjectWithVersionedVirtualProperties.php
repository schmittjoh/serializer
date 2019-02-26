<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Until;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * dummy comment
 *
 * @VirtualProperty(
 *     "classlow",
 *     exp="object.getVirtualValue(1)",
 *     options={@Until("8")}
 * )
 * @VirtualProperty(
 *     "classhigh",
 *     exp="object.getVirtualValue(8)",
 *     options={@Since("6")}
 * )
 */
class ObjectWithVersionedVirtualProperties
{
    /**
     * @Groups({"versions"})
     * @VirtualProperty
     * @SerializedName("low")
     * @Until("8")
     */
    public function getVirualLowValue()
    {
        return 1;
    }

    /**
     * @Groups({"versions"})
     * @VirtualProperty
     * @SerializedName("high")
     * @Since("6")
     */
    public function getVirualHighValue()
    {
        return 8;
    }

    /**
     * @param int $int
     *
     * @return int
     */
    public function getVirtualValue($int)
    {
        return $int;
    }
}
