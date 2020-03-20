<?php

namespace JMS\Serializer\Tests\Fixtures\Discriminator\Serialization;

use JMS\Serializer\Annotation as JMS;
use ReflectionClass;
use ReflectionException;

abstract class Entity
{
    /**
     * @JMS\Type("int")
     * @JMS\Groups({"base"})
     * @var int
     */
    public $id;

    /**
     * Entity constructor.
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("entityName")
     * @JMS\Groups({"entity.identification"})
     * @throws ReflectionException
     */
    public function getEntityName(): string {
        $reflect = new ReflectionClass($this);
        return $reflect->getShortName();
    }
}
