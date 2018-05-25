<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\VirtualProperty;

class ObjectWithVirtualPropertiesAndDuplicatePropName
{
    protected $id;
    protected $name;

    /**
     *
     * @VirtualProperty(name="foo")
     */
    public function getId()
    {
        return 'value';
    }

    /**
     *
     * @VirtualProperty(name="bar")
     * @Serializer\SerializedName("mood")
     */
    public function getName()
    {
        return 'value';
    }
}
