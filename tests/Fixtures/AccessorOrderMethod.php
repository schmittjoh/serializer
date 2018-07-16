<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/** @Serializer\AccessorOrder("custom",  custom = {"method", "b", "a"}) */
class AccessorOrderMethod
{
    private $b = 'b', $a = 'a';

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("foo")
     *
     * @return string
     */
    public function getMethod()
    {
        return 'c';
    }
}
