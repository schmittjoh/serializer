<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class ObjectWithIterable
{
    /**
     * @Serializer\Type("iterable<string,string>")
     * @Serializer\XmlKeyValuePairs
     *
     * @var iterable<string, string>
     */
    #[Serializer\Type(name: 'iterable<string,string>')]
    #[Serializer\XmlKeyValuePairs]
    public $iterable;

    public function __construct(iterable $iterable)
    {
        $this->iterable = $iterable;
    }
}
