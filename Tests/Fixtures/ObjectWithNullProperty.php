<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

use JMS\SerializerBundle\Tests\Fixtures\SimpleObject;

class ObjectWithNullProperty extends SimpleObject
{
    private $nullProperty = null;
}
