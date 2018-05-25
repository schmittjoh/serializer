<?php

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY","METHOD","ANNOTATION"})
 */
final class MaxDepth
{
    /**
     * @Required
     * @var integer
     */
    public $depth;
}
