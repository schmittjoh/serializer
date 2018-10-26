<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Serializer\EventDispatcher\Subscriber;

use JMS\Serializer\Context;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\EventDispatcher\Subscriber\DoctrineProxySubscriber;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Tests\Fixtures\ExclusionStrategy\AlwaysExcludeExclusionStrategy;
use JMS\Serializer\Tests\Fixtures\SimpleObject;
use JMS\Serializer\Tests\Fixtures\SimpleObjectProxy;
use Metadata\MetadataFactoryInterface;
use PHPUnit\Framework\TestCase;

class DoctrineProxySubscriberTest extends TestCase
{
    /** @var Context */
    private $context;

    /** @var DoctrineProxySubscriber */
    private $subscriber;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    public function testRewritesProxyClassName()
    {
        $event = $this->createEvent($obj = new SimpleObjectProxy('a', 'b'), ['name' => get_class($obj), 'params' => []]);
        $this->subscriber->onPreSerialize($event);

        self::assertEquals(['name' => get_parent_class($obj), 'params' => []], $event->getType());
        self::assertTrue($obj->__isInitialized());
    }

    public function testDoesNotRewriteCustomType()
    {
        $event = $this->createEvent($obj = new SimpleObjectProxy('a', 'b'), ['name' => 'FakedName', 'params' => []]);
        $this->subscriber->onPreSerialize($event);

        self::assertEquals(['name' => 'FakedName', 'params' => []], $event->getType());
        self::assertFalse($obj->__isInitialized());
    }

    public function testExcludedPropDoesNotGetInitialized()
    {
        $this->context->method('getExclusionStrategy')->willReturn(new AlwaysExcludeExclusionStrategy());
        $this->context->method('getMetadataFactory')->willReturn(new class implements MetadataFactoryInterface
        {
            public function getMetadataForClass($className)
            {
                return new ClassMetadata(SimpleObjectProxy::class);
            }
        });

        $event = $this->createEvent($obj = new SimpleObjectProxy('a', 'b'), ['name' => SimpleObjectProxy::class, 'params' => []]);
        $this->subscriber->onPreSerialize($event);

        self::assertEquals(['name' => SimpleObjectProxy::class, 'params' => []], $event->getType());
        self::assertFalse($obj->__isInitialized());
    }

    public function testProxyLoadingCanBeSkippedForVirtualTypes()
    {
        $subscriber = new DoctrineProxySubscriber(true);

        $event = $this->createEvent($obj = new SimpleObjectProxy('a', 'b'), ['name' => 'FakedName', 'params' => []]);
        $subscriber->onPreSerialize($event);

        self::assertEquals(['name' => 'FakedName', 'params' => []], $event->getType());
        self::assertFalse($obj->__isInitialized());
    }

    public function testProxyLoadingCanBeSkippedByExclusionStrategy()
    {
        $subscriber = new DoctrineProxySubscriber(false, false);

        $factoryMock = $this->getMockBuilder(MetadataFactoryInterface::class)->getMock();
        $factoryMock->method('getMetadataForClass')->willReturn(new ClassMetadata(SimpleObject::class));

        $this->context->method('getExclusionStrategy')->willReturn(new AlwaysExcludeExclusionStrategy());
        $this->context->method('getMetadataFactory')->willReturn($factoryMock);

        $event = $this->createEvent($obj = new SimpleObjectProxy('a', 'b'), ['name' => SimpleObjectProxy::class, 'params' => []]);
        $subscriber->onPreSerialize($event);
        self::assertFalse($obj->__isInitialized());

        // virtual types are still initialized
        $event = $this->createEvent($obj = new SimpleObjectProxy('a', 'b'), ['name' => 'FakeName', 'params' => []]);
        $subscriber->onPreSerialize($event);
        self::assertTrue($obj->__isInitialized());
    }

    public function testEventTriggeredOnRealClassName()
    {
        $proxy = new SimpleObjectProxy('foo', 'bar');

        $realClassEventTriggered1 = false;
        $this->dispatcher->addListener('serializer.pre_serialize', static function () use (&$realClassEventTriggered1) {
            $realClassEventTriggered1 = true;
        }, get_parent_class($proxy));

        $event = $this->createEvent($proxy, ['name' => get_class($proxy), 'params' => []]);
        $this->dispatcher->dispatch('serializer.pre_serialize', get_class($proxy), 'json', $event);

        self::assertTrue($realClassEventTriggered1);
    }

    public function testListenersCanChangeType()
    {
        $proxy = new SimpleObjectProxy('foo', 'bar');

        $realClassEventTriggered1 = false;
        $this->dispatcher->addListener('serializer.pre_serialize', static function (PreSerializeEvent $event) {
            $event->setType('foo', ['bar']);
        }, get_parent_class($proxy));

        $event = $this->createEvent($proxy, ['name' => get_class($proxy), 'params' => []]);
        $this->dispatcher->dispatch('serializer.pre_serialize', get_class($proxy), 'json', $event);

        self::assertSame(['name' => 'foo', 'params' => ['bar']], $event->getType());
    }

    public function testListenersDoNotChangeTypeOnProxiesAndVirtualTypes()
    {
        $proxy = new SimpleObjectProxy('foo', 'bar');

        $event = $this->createEvent($proxy, ['name' => 'foo', 'params' => []]);
        $this->dispatcher->dispatch('serializer.pre_serialize', get_class($proxy), 'json', $event);

        self::assertSame(['name' => 'foo', 'params' => []], $event->getType());
    }

    public function testOnPreSerializeMaintainsParams()
    {
        $object = new SimpleObjectProxy('foo', 'bar');
        $type = ['name' => SimpleObjectProxy::class, 'params' => ['baz']];

        $event = $this->createEvent($object, $type);
        $this->subscriber->onPreSerialize($event);

        self::assertSame(['name' => SimpleObject::class, 'params' => ['baz']], $event->getType());
    }

    protected function setUp()
    {
        $this->subscriber = new DoctrineProxySubscriber();
        $this->context = $this->getMockBuilder(Context::class)->getMock();

        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addSubscriber($this->subscriber);
    }

    private function createEvent($object, array $type)
    {
        return new PreSerializeEvent($this->context, $object, $type);
    }
}
