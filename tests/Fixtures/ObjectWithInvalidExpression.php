<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\VirtualProperty(
 *     "invalid",
 *     exp="oinvalid"
 * )
 */
class ObjectWithInvalidExpression
{
    /**
     * @var @Serializer\Exclude(if="inval")
     */
    private $prop1;

    /**
     * @var @Serializer\Expose(if="invalid")
     */
    private $prop2;
}
