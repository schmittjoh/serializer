<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Benchmark\Performance;

use JMS\Serializer\Type\CachingParser;
use JMS\Serializer\Type\Parser;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;

/**
 * @BeforeMethods({"setUp"})
 * @Iterations(5)
 * @Revs(1000)
 */
class TypeParsingBench
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var CachingParser
     */
    private $cachingParser;

    /**
     * @var string[]
     */
    private $types = [
        'string',
        'int',
        'array<string>',
        'array<string, int>',
        'DateTime<\'Y-m-d\'>',
        'DateTime<\'Y-m-d\TH:i:s\', \'UTC\'>',
        'array<App\Entity\User>',
        'Foo<Bar<Baz, Qux>>',
        'DateTime<null, null, [\'Y-m-d\TH:i:s\', \'Y-m-d\TH:i:sP\']>',
    ];

    public function setUp(): void
    {
        $this->parser = new Parser();
        $this->cachingParser = new CachingParser(new Parser());
    }

    public function benchParserWithoutCache(): void
    {
        foreach ($this->types as $type) {
            $this->parser->parse($type);
        }
    }

    public function benchParserWithCache(): void
    {
        foreach ($this->types as $type) {
            $this->cachingParser->parse($type);
        }
    }
}
