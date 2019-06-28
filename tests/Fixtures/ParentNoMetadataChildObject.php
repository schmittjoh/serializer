<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class ParentNoMetadataChildObject extends ParentNoMetadata
{
    /**
     * @Serializer\Type("string")
     *
     * @var string
     */
    public $bar;
}
