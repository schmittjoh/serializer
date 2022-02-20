<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * @ExclusionPolicy(policy="all")
 */
#[ExclusionPolicy(policy: 'all')]
class AllExcludedObject
{
    private $foo = 'foo';

    /**
     * @Expose
     */
    #[Expose]
    private $bar = 'bar';
}
