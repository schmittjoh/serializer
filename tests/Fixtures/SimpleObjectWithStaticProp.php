<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

class SimpleObjectWithStaticProp
{
    /** @Type("string") */
    private $foo;

    /**
     * @SerializedName("moo")
     * @Type("string")
     */
    private static $bar;

    /** @Type("string") */
    protected static $camelCase = 'boo';

    public function __construct($foo, $bar)
    {
        $this->foo = $foo;
        self::$bar = $bar;
    }
}
