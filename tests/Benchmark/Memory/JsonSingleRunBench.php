<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Benchmark\Memory;

use JMS\Serializer\Tests\Benchmark\AbstractSerializationBench;

class JsonSingleRunBench extends AbstractSerializationBench
{
    public function __construct()
    {
        $this->amountOfComments = 1;
        $this->amountOfPosts = 1;
        parent::__construct();
    }

    protected function getFormat(): string
    {
        return 'json';
    }
}
