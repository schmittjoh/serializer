<?php

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "CLASS", "METHOD", "ANNOTATION"})
 */
final class Exclude
{
    public $if;
}
