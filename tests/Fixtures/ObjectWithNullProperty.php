<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;

class ObjectWithNullProperty extends SimpleObject
{
    /**
     * @var null
     * @Type("string")
     */
    private $nullProperty = null;

    /**
     * @return null
     */
    public function getNullProperty()
    {
        return $this->nullProperty;
    }
}
