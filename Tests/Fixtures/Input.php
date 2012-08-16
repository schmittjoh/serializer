<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

use JMS\SerializerBundle\Annotation as Serializer;

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
