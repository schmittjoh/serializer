<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Exclusion;

use JMS\Serializer\Exclusion\DisjunctExclusionStrategy;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\SerializationContext;
use PHPUnit\Framework\TestCase;

class DisjunctExclusionStrategyTest extends TestCase
{
    public function testShouldSkipClassShortCircuiting()
    {
        $metadata = new ClassMetadata('stdClass');
        $context = SerializationContext::create();

        $strat = new DisjunctExclusionStrategy([
            $first = $this->getMockBuilder('JMS\Serializer\Exclusion\ExclusionStrategyInterface')->getMock(),
            $last = $this->getMockBuilder('JMS\Serializer\Exclusion\ExclusionStrategyInterface')->getMock(),
        ]);

        $first->expects($this->once())
            ->method('shouldSkipClass')
            ->with($metadata, $context)
            ->will($this->returnValue(true));

        $last->expects($this->never())
            ->method('shouldSkipClass');

        self::assertTrue($strat->shouldSkipClass($metadata, $context));
    }

    public function testShouldSkipClassDisjunctBehavior()
    {
        $metadata = new ClassMetadata('stdClass');
        $context = SerializationContext::create();

        $strat = new DisjunctExclusionStrategy([
            $first = $this->getMockBuilder('JMS\Serializer\Exclusion\ExclusionStrategyInterface')->getMock(),
            $last = $this->getMockBuilder('JMS\Serializer\Exclusion\ExclusionStrategyInterface')->getMock(),
        ]);

        $first->expects($this->once())
            ->method('shouldSkipClass')
            ->with($metadata, $context)
            ->will($this->returnValue(false));

        $last->expects($this->once())
            ->method('shouldSkipClass')
            ->with($metadata, $context)
            ->will($this->returnValue(true));

        self::assertTrue($strat->shouldSkipClass($metadata, $context));
    }

    public function testShouldSkipClassReturnsFalseIfNoPredicateMatched()
    {
        $metadata = new ClassMetadata('stdClass');
        $context = SerializationContext::create();

        $strat = new DisjunctExclusionStrategy([
            $first = $this->getMockBuilder('JMS\Serializer\Exclusion\ExclusionStrategyInterface')->getMock(),
            $last = $this->getMockBuilder('JMS\Serializer\Exclusion\ExclusionStrategyInterface')->getMock(),
        ]);

        $first->expects($this->once())
            ->method('shouldSkipClass')
            ->with($metadata, $context)
            ->will($this->returnValue(false));

        $last->expects($this->once())
            ->method('shouldSkipClass')
            ->with($metadata, $context)
            ->will($this->returnValue(false));

        self::assertFalse($strat->shouldSkipClass($metadata, $context));
    }

    public function testShouldSkipPropertyShortCircuiting()
    {
        $metadata = new StaticPropertyMetadata('stdClass', 'foo', 'bar');
        $context = SerializationContext::create();

        $strat = new DisjunctExclusionStrategy([
            $first = $this->getMockBuilder('JMS\Serializer\Exclusion\ExclusionStrategyInterface')->getMock(),
            $last = $this->getMockBuilder('JMS\Serializer\Exclusion\ExclusionStrategyInterface')->getMock(),
        ]);

        $first->expects($this->once())
            ->method('shouldSkipProperty')
            ->with($metadata, $context)
            ->will($this->returnValue(true));

        $last->expects($this->never())
            ->method('shouldSkipProperty');

        self::assertTrue($strat->shouldSkipProperty($metadata, $context));
    }

    public function testShouldSkipPropertyDisjunct()
    {
        $metadata = new StaticPropertyMetadata('stdClass', 'foo', 'bar');
        $context = SerializationContext::create();

        $strat = new DisjunctExclusionStrategy([
            $first = $this->getMockBuilder('JMS\Serializer\Exclusion\ExclusionStrategyInterface')->getMock(),
            $last = $this->getMockBuilder('JMS\Serializer\Exclusion\ExclusionStrategyInterface')->getMock(),
        ]);

        $first->expects($this->once())
            ->method('shouldSkipProperty')
            ->with($metadata, $context)
            ->will($this->returnValue(false));

        $last->expects($this->once())
            ->method('shouldSkipProperty')
            ->with($metadata, $context)
            ->will($this->returnValue(true));

        self::assertTrue($strat->shouldSkipProperty($metadata, $context));
    }

    public function testShouldSkipPropertyReturnsFalseIfNoPredicateMatches()
    {
        $metadata = new StaticPropertyMetadata('stdClass', 'foo', 'bar');
        $context = SerializationContext::create();

        $strat = new DisjunctExclusionStrategy([
            $first = $this->getMockBuilder('JMS\Serializer\Exclusion\ExclusionStrategyInterface')->getMock(),
            $last = $this->getMockBuilder('JMS\Serializer\Exclusion\ExclusionStrategyInterface')->getMock(),
        ]);

        $first->expects($this->once())
            ->method('shouldSkipProperty')
            ->with($metadata, $context)
            ->will($this->returnValue(false));

        $last->expects($this->once())
            ->method('shouldSkipProperty')
            ->with($metadata, $context)
            ->will($this->returnValue(false));

        self::assertFalse($strat->shouldSkipProperty($metadata, $context));
    }
}
