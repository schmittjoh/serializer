<?php

namespace JMS\Serializer\Tests\Exclusion;

use JMS\Serializer\Exclusion\ExpressionLanguageExclusionStrategy;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\SerializationContext;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
class ExpressionLanguageExclusionStrategyTest extends \PHPUnit_Framework_TestCase
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
            ->with('foo', array(
                'context' => $this->context,
                'property_metadata' => $metadata,
                'object' => $this->visitedObject,
            ))
            ->willReturn(true);

        $this->assertSame(true, $this->exclusionStrategy->shouldSkipProperty($metadata, $this->context));
    }

    public function testExpressionLanguageSkipsWhenNoExpression()
    {
        $metadata = new StaticPropertyMetadata('stdClass', 'prop', 'propVal');

        $this->expressionEvaluator->expects($this->never())->method('evaluate');

        $this->assertSame(false, $this->exclusionStrategy->shouldSkipProperty($metadata, $this->context));
    }
}
