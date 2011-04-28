<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

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