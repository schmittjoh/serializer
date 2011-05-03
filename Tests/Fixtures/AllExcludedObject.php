<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

use JMS\SerializerBundle\Annotation\Expose;
use JMS\SerializerBundle\Annotation\ExclusionPolicy;

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