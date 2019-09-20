<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * @Serializer\ExclusionPolicy("ALL")
 */
class ObjectWithVirtualPropertiesAndDuplicatePropNameExcludeAll
{
    protected $name;

    /**
     * @Serializer\SerializedName("mood")
     *
     * @VirtualProperty()
     */
    public function getName()
    {
        return 'value';
    }
}
