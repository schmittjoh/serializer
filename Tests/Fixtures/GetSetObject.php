<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

use JMS\SerializerBundle\Annotation\AccessType;
use JMS\SerializerBundle\Annotation\Type;

/** @AccessType("public_method") */
class GetSetObject
{
    /** @AccessType("property") @Type("integer") */
    private $id = 1;

    /** @Type("string") */
    private $name = 'Foo';

    public function getId()
    {
        throw new \RuntimeException('This should not be called.');
    }

    public function getName()
    {
        return 'Johannes';
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}