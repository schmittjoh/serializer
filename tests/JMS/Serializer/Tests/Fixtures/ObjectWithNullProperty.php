<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Tests\Fixtures\SimpleObject;

class ObjectWithNullProperty extends SimpleObject
{
    private $nullProperty = null;
}
