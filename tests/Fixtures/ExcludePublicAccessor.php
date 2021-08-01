<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\AccessType;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\ReadOnlyProperty;

/**
 * @AccessType("public_method")
 * @ReadOnlyProperty
 */
#[AccessType(type: 'public_method')]
#[ReadOnlyProperty]
class ExcludePublicAccessor
{
    /**
     * @Exclude
     * @var mixed
     */
    #[Exclude]
    private $iShallNotBeAccessed;

    /**
     * @var int
     */
    private $id = 1;

    public function getId()
    {
        return $this->id;
    }
}
