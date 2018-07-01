<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

abstract class XmlCollection
{
    /**
     * @var string
     */
    public $entry = 'entry';

    /**
     * @var bool
     */
    public $inline = false;

    /**
     * @var string
     */
    public $namespace;

    /**
     * @var bool
     */
    public $skipWhenEmpty = true;
}
