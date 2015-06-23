<?php

namespace JMS\Serializer\Tests\Exclusion;

use JMS\Serializer\Exclusion\ExcludeForGroupsStrategy;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\SerializationContext;

class ExcludeForGroupsStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testNoGroupsShouldNotSkipClass()
    {
        $classMetadata = new ClassMetadata('stdClass');
        $context = SerializationContext::create();

        $excludeForGroupsStrategy = new ExcludeForGroupsStrategy(['testGroup']);
        $this->assertFalse(
            $excludeForGroupsStrategy->shouldSkipClass($classMetadata, $context)
        );
    }

    public function testNoGroupsDefined()
    {
        $metadata = new StaticPropertyMetadata('stdClass', 'foo', 'bar');
        $context = SerializationContext::create();

        $excludeForGroupsStrategy = new ExcludeForGroupsStrategy(['testGroup']);
        $this->assertFalse(
            $excludeForGroupsStrategy->shouldSkipProperty($metadata, $context)
        );
    }

    public function testShouldNotSkipProperty()
    {
        $metadata = new StaticPropertyMetadata('stdClass', 'foo', 'bar');
        $metadata->excludeForGroups = ['someGroup'];
        $context = SerializationContext::create();

        $excludeForGroupsStrategy = new ExcludeForGroupsStrategy(['testGroup']);
        $this->assertFalse(
            $excludeForGroupsStrategy->shouldSkipProperty($metadata, $context)
        );
    }

    public function testShouldSkipProperty()
    {
        $metadata = new StaticPropertyMetadata('stdClass', 'foo', 'bar');
        $metadata->excludeForGroups = ['testGroup'];
        $context = SerializationContext::create();

        $excludeForGroupsStrategy = new ExcludeForGroupsStrategy(['testGroup']);
        $this->assertTrue(
            $excludeForGroupsStrategy->shouldSkipProperty($metadata, $context)
        );
    }
}
