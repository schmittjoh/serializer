<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class ObjectWithEmptyHash
{
    /**
     * @Serializer\Type("array<string,string>")
     * @Serializer\XmlList(skipWhenEmpty=false)
     */
    private $hash = array();
}
