<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

class SimpleObjectWithStaticProp
{
    /** @Type("string") */
    #[Type(name: 'string')]
    private $foo;

    /**
     * @SerializedName("moo")
     * @Type("string")
     */
    #[SerializedName(name: 'moo')]
    #[Type(name: 'string')]
    private static $bar;

    /** @Type("string") */
    #[Type(name: 'string')]
    protected static $camelCase = 'boo';

    public function __construct($foo, $bar)
    {
        $this->foo = $foo;
        self::$bar = $bar;
    }
}
