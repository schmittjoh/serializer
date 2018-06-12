<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\VirtualProperty("firstName", exp="object.getFirstName()")
 * @Serializer\VirtualProperty("direction", exp="context.getDirection()")
 * @Serializer\VirtualProperty("name", exp="property_metadata.name")
 */
class AuthorExpressionAccessContext
{
    /**
     * @Serializer\Exclude()
     */
    private $firstName;

    public function __construct($firstName)
    {
        $this->firstName = $firstName;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }
}
