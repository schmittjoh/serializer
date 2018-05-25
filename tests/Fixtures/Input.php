<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("input")
 */
class Input
{
    /**
     * @Serializer\XmlAttributeMap
     */
    private $attributes;

    public function __construct($attributes = null)
    {
        $this->attributes = $attributes ?: [
            'type' => 'text',
            'name' => 'firstname',
            'value' => 'Adrien',
        ];
    }
}
