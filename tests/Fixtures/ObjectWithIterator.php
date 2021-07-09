<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use Iterator;
use JMS\Serializer\Annotation as Serializer;

class ObjectWithIterator
{
    /**
     * @Serializer\Type("Iterator<string,string>")
     * @Serializer\XmlKeyValuePairs
     *
     * @var Iterator
     */
    #[Serializer\Type(name: 'Iterator<string,string>')]
    #[Serializer\XmlKeyValuePairs]
    public $iterator;

    public function __construct(Iterator $iterator)
    {
        $this->iterator = $iterator;
    }
}
