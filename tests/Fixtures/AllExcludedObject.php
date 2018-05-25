<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @ExclusionPolicy("all")
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class AllExcludedObject
{
    private $foo = 'foo';

    /**
     * @Expose
     */
    private $bar = 'bar';
}
