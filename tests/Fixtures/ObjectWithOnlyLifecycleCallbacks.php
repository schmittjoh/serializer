<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\PostDeserialize;
use JMS\Serializer\Annotation\PostSerialize;
use JMS\Serializer\Annotation\PreSerialize;

class ObjectWithOnlyLifecycleCallbacks
{
    /**
     * @PreSerialize
     */
    #[PreSerialize]
    private function prepareForSerialization()
    {
    }

    /**
     * @PostSerialize
     */
    #[PostSerialize]
    private function cleanUpAfterSerialization()
    {
    }

    /**
     * @PostDeserialize
     */
    #[PostDeserialize]
    private function afterDeserialization()
    {
    }
}
