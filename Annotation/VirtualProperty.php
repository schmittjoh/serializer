<?php

namespace JMS\SerializerBundle\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 *
 * @author Alexander Klimenkov <alx.devel@gmail.com>
 */
final class VirtualProperty
{
    /**
     * @Required
     * @var string
     */
    public $field;
}