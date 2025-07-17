<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Benchmark\Memory;

class XmlMultipleRunBench extends XmlSingleRunBench
{
    public function __construct()
    {
        $this->iterations = 10000;

        parent::__construct();
    }

    protected function getFormat(): string
    {
        return 'xml';
    }
}
