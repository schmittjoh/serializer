<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * @VirtualProperty(
 *     "virtualValue",
 *     exp="object.getVirtualValue()"
 * )
 * @ExclusionPolicy("all")
 */
class ObjectWithExpressionVirtualPropertiesAndExcludeAll
{

    public function getVirtualValue()
    {
        return 'value';
    }
}
