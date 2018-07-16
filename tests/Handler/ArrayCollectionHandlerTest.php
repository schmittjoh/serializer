<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Handler\ArrayCollectionHandler;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Tests\Fixtures\ExclusionStrategy\AlwaysExcludeExclusionStrategy;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use Metadata\MetadataFactoryInterface;
use PHPUnit\Framework\TestCase;

class ArrayCollectionHandlerTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testSerializeArray()
    {
        $handler = new ArrayCollectionHandler();

        $visitor = $this->getMockBuilder(SerializationVisitorInterface::class)->getMock();
        $visitor->method('visitArray')->with(['foo'])->willReturn(['foo']);

        $context = $this->getMockBuilder(SerializationContext::class)->getMock();
        $type = ['name' => 'ArrayCollection', 'params' => []];

        $collection = new ArrayCollection(['foo']);

        $handler->serializeCollection($visitor, $collection, $type, $context);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testSerializeArraySkipByExclusionStrategy()
    {
        $handler = new ArrayCollectionHandler(false);

        $visitor = $this->getMockBuilder(SerializationVisitorInterface::class)->getMock();
        $visitor->method('visitArray')->with([])->willReturn([]);

        $context = $this->getMockBuilder(SerializationContext::class)->getMock();

        $factoryMock = $this->getMockBuilder(MetadataFactoryInterface::class)->getMock();
        $factoryMock->method('getMetadataForClass')->willReturn(new ClassMetadata(ArrayCollection::class));

        $context->method('getExclusionStrategy')->willReturn(new AlwaysExcludeExclusionStrategy());
        $context->method('getMetadataFactory')->willReturn($factoryMock);

        $type = ['name' => 'ArrayCollection', 'params' => []];

        $collection = new ArrayCollection(['foo']);

        $handler->serializeCollection($visitor, $collection, $type, $context);
    }
}
