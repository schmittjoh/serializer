<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * @ExclusionPolicy("all")
 */
class ObjectWithVirtualPropertiesAndExcludeAll
{
    /**
     * @VirtualProperty
     */
    public function getVirtualValue()
    {
        return 'value';
    }
}
