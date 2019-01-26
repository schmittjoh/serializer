<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class ObjectWithIterator
{
    /**
     * @Serializer\Type("Iterator<string,string>")
     * @Serializer\XmlKeyValuePairs
     *
     * @var \Iterator
     */
    public $iterator;

    public function __construct(\Iterator $iterator)
    {
        $this->iterator = $iterator;
    }
}
