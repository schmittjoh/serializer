<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class ObjectWithTypedArraySetter
{
    /**
     * @Serializer\Type("array<string>")
     * @Serializer\AccessType(type="public_method")
     */
    private $empty = [];

    public function setEmpty(array $empty): void
    {
        $this->empty = $empty;
    }

    public function getEmpty(): array
    {
        return $this->empty;
    }
}
