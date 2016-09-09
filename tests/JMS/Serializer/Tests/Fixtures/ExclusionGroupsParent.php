<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;

class ExclusionGroupsParent
{
    /**
     * @Type("JMS\Serializer\Tests\Fixtures\ExclusionGroupsObject")
     */
    public $exclusionGroupsObject;

    public function __construct()
    {
        $this->exclusionGroupsObject = new ExclusionGroupsObject();
    }
}