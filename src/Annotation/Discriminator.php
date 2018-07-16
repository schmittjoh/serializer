<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Discriminator
{
    /** @var array<string> */
    public $map;

    /** @var string */
    public $field = 'type';

    /** @var bool */
    public $disabled = false;

    /** @var string[] */
    public $groups = [];
}
