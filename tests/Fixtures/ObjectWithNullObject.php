<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\DeserializeNull;
use JMS\Serializer\Annotation\Type;

class ObjectWithNullObject
{
    /**
     * @var null
     * @Type("NullObject")
     * @DeserializeNull()
     */
    private $nullProperty;

    /**
     * @return null
     */
    public function getNullProperty()
    {
        return $this->nullProperty;
    }
}
