<?php

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
        $this->attributes = $attributes ?: array(
            'type' => 'text',
            'name' => 'firstname',
            'value' => 'Adrien',
        );
    }
}
