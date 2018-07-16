<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

abstract class Version
{
    /**
     * @Required
     * @var string
     */
    public $version;
}
