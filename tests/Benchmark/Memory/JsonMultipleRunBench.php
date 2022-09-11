<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Benchmark\Memory;

class JsonMultipleRunBench extends JsonSingleRunBench
{
    public function __construct()
    {
        $this->iterations = 10000;
        parent::__construct();
    }
}
