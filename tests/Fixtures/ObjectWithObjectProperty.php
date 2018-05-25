<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;


class ObjectWithObjectProperty
{
    /**
     * @Type("string")
     */
    private $foo;

    /**
     * @Type("JMS\Serializer\Tests\Fixtures\Author")
     */
    private $author;

    /**
     * @return string
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * @return \JMS\Serializer\Tests\Fixtures\Author
     */
    public function getAuthor()
    {
        return $this->author;
    }


}
