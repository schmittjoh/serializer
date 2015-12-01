<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Type;

class ExclusionGroupsObject
{
    /**
     * @var string
     * @Type("string")
     * @Exclude({"testExclusionGroup1", "testExclusionGroup2"})
     */
    public $foo = 'foo';

    /**
     * @var string
     * @Type("string")
     * @Exclude({"testExclusionGroup1"})
     */
    public $foo2 = 'foo2';

    /**
     * @var string
     * @Type("string")
     */
    public $bar = 'bar';

    /**
     * @var string
     * @Type("string")
     * @Exclude()
     */
    public $neverShown = 'nevershown';
}