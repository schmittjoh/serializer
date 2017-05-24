<?php

/*
 * Copyright 2016 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
