<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;

class ObjectWithStaticProperty
{
    /**
     * @Type("string")
     */
    private $foo;

    /**
     * @Type("JMS\Serializer\Tests\Fixtures\Author")
     */
    private static $author;

    /**
     * @return string
     */
    public function getFoo()
    {
        return $this->foo;
    }
}
