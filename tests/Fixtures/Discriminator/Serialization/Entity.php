<?php

declare(strict_types=1);

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

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    /**
     * @throws ReflectionException
     *
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("entityName")
     * @JMS\Groups({"entity.identification"})
     */
    public function getEntityName(): string
    {
        $reflect = new ReflectionClass($this);
        return $reflect->getShortName();
    }
}
