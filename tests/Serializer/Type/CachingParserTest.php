<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Serializer\Type;

use JMS\Serializer\Type\CachingParser;
use JMS\Serializer\Type\Parser;
use JMS\Serializer\Type\ParserInterface;
use PHPUnit\Framework\TestCase;

class CachingParserTest extends TestCase
{
    /** @var CachingParser */
    private $parser;

    protected function setUp(): void
    {
        $this->parser = new CachingParser(new Parser());
    }

    public function testReturnsSameResultAsInnerParser(): void
    {
        $inner = new Parser();
        $cached = new CachingParser($inner);

        $types = [
            'string',
            'int',
            'array<Foo>',
            'array<string, int>',
            'DateTime<\'Y-m-d\'>',
            'Foo<Bar<Baz>>',
        ];

        foreach ($types as $type) {
            self::assertSame($inner->parse($type), $cached->parse($type), sprintf('Mismatch for type "%s"', $type));
        }
    }

    public function testCachesResults(): void
    {
        $inner = $this->createMock(ParserInterface::class);
        $inner->expects(self::once())
            ->method('parse')
            ->with('string')
            ->willReturn(['name' => 'string', 'params' => []]);

        $cached = new CachingParser($inner);

        $result1 = $cached->parse('string');
        $result2 = $cached->parse('string');

        self::assertSame($result1, $result2);
    }

    public function testDifferentTypesAreNotConfused(): void
    {
        $result1 = $this->parser->parse('string');
        $result2 = $this->parser->parse('int');

        self::assertSame(['name' => 'string', 'params' => []], $result1);
        self::assertSame(['name' => 'int', 'params' => []], $result2);
    }

    public function testComplexTypesAreCached(): void
    {
        $type = 'DateTime<\'Y-m-d\', \'UTC\'>';
        $result1 = $this->parser->parse($type);
        $result2 = $this->parser->parse($type);

        self::assertSame($result1, $result2);
        self::assertSame('DateTime', $result1['name']);
        self::assertCount(2, $result1['params']);
    }
}
