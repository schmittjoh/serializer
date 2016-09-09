<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Type;

/**
 * @Exclude({"noClass"})
 */
class ExclusionGroupsObject
{
    /**
     * @Type("string")
     * @Exclude({"testExclusionGroup1", "testExclusionGroup2"})
     */
    public $foo = 'foo';

    /**
     * @Type("string")
     * @Exclude({"testExclusionGroup1"})
     */
    public $foo2 = 'foo2';

    /**
     * @Type("string")
     */
    public $bar = 'bar';

    /**
     * @Type("string")
     * @Exclude()
     */
    public $neverShown = 'nevershown';
}