<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Context;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 */
class PersonSecretWithVariables
{
    /**
     * @Serializer\Type("string")
     */
    public $name;

    /**
     * @Serializer\Type("string")
     * @Serializer\Expose(if="context.getDirection()==2 || object.test(property_metadata, context)")
     */
    public $gender;


    public function test(PropertyMetadata $propertyMetadata, Context $context)
    {
        return true;
    }
}
