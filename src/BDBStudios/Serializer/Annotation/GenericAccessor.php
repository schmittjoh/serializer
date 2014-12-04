<?php

namespace BDBStudios\Serializer\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class GenericAccessor
{

    /**
     * @var string
     */
    public $getter;

    /**
     * @var string
     */
    public $setter;

    /**
     * @var string
     */
    public $propertyName;
}
