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
#[Serializer\VirtualProperty(name: 'invalid', exp: 'oinvalid')]
class ObjectWithInvalidExpression
{
    /**
     * @Serializer\Exclude(if="inval")
     */
    #[Serializer\Exclude(if: 'inval')]
    private $prop1;

    /**
     * @Serializer\Expose(if="invalid")
     */
    #[Serializer\Expose(if: 'invalid')]
    private $prop2;
}
