<?php

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD","ANNOTATION"})
 */
final class Expose
{
    public $if;
}
