<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\AccessType;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\ReadOnlyProperty;
use JMS\Serializer\Annotation\Type;

/** @AccessType("public_method") */
#[AccessType(type: 'public_method')]
class GetSetObject
{
    /** @AccessType("property") @Type("integer") */
    #[AccessType(type: 'property')]
    #[Type(name: 'integer')]
    private $id = 1;

    /** @Type("string") */
    #[Type(name: 'string')]
    private $name = 'Foo';

    /**
     * @ReadOnlyProperty
     */
    #[ReadOnlyProperty]
    private $readOnlyProperty = 42;

    /**
     * This property should be exlcluded
     *
     * @Exclude()
     */
    #[Exclude]
    private $excludedProperty;

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

    public function getReadOnlyProperty()
    {
        return $this->readOnlyProperty;
    }
}
