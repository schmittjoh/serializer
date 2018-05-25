<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY","METHOD","ANNOTATION"})
 */
final class XmlMap extends XmlCollection
{
    /**
     * @var string
     */
    public $keyAttribute = '_key';
}
