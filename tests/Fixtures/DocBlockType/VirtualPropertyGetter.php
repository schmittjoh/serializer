<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType;

use JMS\Serializer\Annotation as Serializer;

class VirtualPropertyGetter
{
    /**
     * @Serializer\VirtualProperty
     *
     * @return string[]
     */
    #[Serializer\VirtualProperty]
    public function getArrayOfStrings(): array
    {
        return ['foo', 'bar'];
    }
}
