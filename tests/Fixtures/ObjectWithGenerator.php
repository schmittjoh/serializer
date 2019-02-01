<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;

class ObjectWithGenerator
{
    /**
     * @Serializer\Type("Generator<string,string>")
     * @Serializer\XmlKeyValuePairs
     *
     * @var \Generator
     */
    public $generator;

    public function __construct(\Generator $generator)
    {
        $this->generator = $generator;
    }
}
