<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\Exclude("NONE")
 */
class SimpleInternalObject extends \Exception
{
    private $bar;

    protected $camelCase = 'boo';

    public function __construct($foo, $bar)
    {
        parent::__construct($foo);
        $this->bar = $bar;
    }
}
