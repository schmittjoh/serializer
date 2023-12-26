<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\VirtualProperty;

#[MissingAttribute]
final class MissingAttributeObject
{
    #[MissingAttribute]
    public $property;

    /**
     * @VirtualProperty(name="propertyFromMethod")
     */
    #[MissingAttribute]
    #[VirtualProperty(name: 'propertyFromMethod')]
    public function propertyMethod()
    {
        return '';
    }
}
