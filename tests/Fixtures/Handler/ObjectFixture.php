<?php

namespace JMS\Serializer\Tests\Fixtures\Handler;

use JMS\Serializer\Annotation as JMS;

class ObjectFixture
{
    /**
     * @JMS\Type("integer")
     */
    private $id;

    /**
     * @JMS\Type("Object")
     */
    private $object;

    public function setProperties()
    {
        $this->id = 3;
        $this->object = new DynamicObject();
        $this->object->setProperties();
        return $this;
    }
}
