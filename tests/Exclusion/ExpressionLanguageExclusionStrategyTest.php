<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Exclusion;

use JMS\Serializer\Exclusion\ExpressionLanguageExclusionStrategy;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\SerializationContext;
use PHPUnit\Framework\TestCase;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
class ExpressionLanguageExclusionStrategyTest extends TestCase
{
    private $visitedObject;
    private $context;
    private $expressionEvaluator;
    private $exclusionStrategy;

    public function setUp()
    {
        $this->visitedObject = new \stdClass();

        $this->context = $this->getMockBuilder(SerializationContext::class)->getMock();
        $this->context->method('getObject')->willReturn($this->visitedObject);

        $this->expressionEvaluator = $this->getMockBuilder(ExpressionEvaluator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->exclusionStrategy = new ExpressionLanguageExclusionStrategy($this->expressionEvaluator);
    }

    public function testExpressionLanguageExclusionWorks()
    {
        $metadata = new StaticPropertyMetadata('stdClass', 'prop', 'propVal');
        $metadata->excludeIf = 'foo';

        $this->expressionEvaluator->expects($this->once())
            ->method('evaluate')
            ->with('foo', [
                'context' => $this->context,
                'property_metadata' => $metadata,
                'object' => $this->visitedObject,
            ])
            ->willReturn(true);

        self::assertTrue($this->exclusionStrategy->shouldSkipProperty($metadata, $this->context));
    }

    public function testExpressionLanguageSkipsWhenNoExpression()
    {
        $metadata = new StaticPropertyMetadata('stdClass', 'prop', 'propVal');

        $this->expressionEvaluator->expects($this->never())->method('evaluate');

        self::assertFalse($this->exclusionStrategy->shouldSkipProperty($metadata, $this->context));
    }
}
