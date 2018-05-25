<?php

namespace JMS\Serializer\Tests\Serializer\EventDispatcher\Subscriber;

use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\EventDispatcher\Subscriber\DoctrineProxySubscriber;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Tests\Fixtures\ExclusionStrategy\AlwaysExcludeExclusionStrategy;
use JMS\Serializer\Tests\Fixtures\SimpleObject;
use JMS\Serializer\Tests\Fixtures\SimpleObjectProxy;
use JMS\Serializer\VisitorInterface;
use Metadata\MetadataFactoryInterface;

class DoctrineProxySubscriberTest extends \PHPUnit_Framework_TestCase
{
    /** @var VisitorInterface */
    private $visitor;

    /** @var DoctrineProxySubscriber */
    private $subscriber;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    public function testRewritesProxyClassName()
    {
        $event = $this->createEvent($obj = new SimpleObjectProxy('a', 'b'), array('name' => get_class($obj), 'params' => array()));
        $this->subscriber->onPreSerialize($event);

        $this->assertEquals(array('name' => get_parent_class($obj), 'params' => array()), $event->getType());
        $this->assertTrue($obj->__isInitialized());
    }

    public function testDoesNotRewriteCustomType()
    {
        $event = $this->createEvent($obj = new SimpleObjectProxy('a', 'b'), array('name' => 'FakedName', 'params' => array()));
        $this->subscriber->onPreSerialize($event);

        $this->assertEquals(array('name' => 'FakedName', 'params' => array()), $event->getType());
        $this->assertTrue($obj->__isInitialized());
    }

    public function testProxyLoadingCanBeSkippedForVirtualTypes()
    {
        $subscriber = new DoctrineProxySubscriber(true);

        $event = $this->createEvent($obj = new SimpleObjectProxy('a', 'b'), array('name' => 'FakedName', 'params' => array()));
        $subscriber->onPreSerialize($event);

        $this->assertEquals(array('name' => 'FakedName', 'params' => array()), $event->getType());
        $this->assertFalse($obj->__isInitialized());
    }

    public function testProxyLoadingCanBeSkippedByExclusionStrategy()
    {
        $subscriber = new DoctrineProxySubscriber(false, false);

        $factoryMock = $this->getMockBuilder(MetadataFactoryInterface::class)->getMock();
        $factoryMock->method('getMetadataForClass')->willReturn(new ClassMetadata(SimpleObject::class));

        $this->visitor->method('getExclusionStrategy')->willReturn(new AlwaysExcludeExclusionStrategy());
        $this->visitor->method('getMetadataFactory')->willReturn($factoryMock);

        $event = $this->createEvent($obj = new SimpleObjectProxy('a', 'b'), array('name' => SimpleObjectProxy::class, 'params' => array()));
        $subscriber->onPreSerialize($event);
        $this->assertFalse($obj->__isInitialized());

        // virtual types are still initialized
        $event = $this->createEvent($obj = new SimpleObjectProxy('a', 'b'), array('name' => 'FakeName', 'params' => array()));
        $subscriber->onPreSerialize($event);
        $this->assertTrue($obj->__isInitialized());
    }

    public function testEventTriggeredOnRealClassName()
    {
        $proxy = new SimpleObjectProxy('foo', 'bar');

        $realClassEventTriggered1 = false;
        $this->dispatcher->addListener('serializer.pre_serialize', function () use (&$realClassEventTriggered1) {
            $realClassEventTriggered1 = true;
        }, get_parent_class($proxy));

        $event = $this->createEvent($proxy, array('name' => get_class($proxy), 'params' => array()));
        $this->dispatcher->dispatch('serializer.pre_serialize', get_class($proxy), 'json', $event);

        $this->assertTrue($realClassEventTriggered1);
    }

    public function testListenersCanChangeType()
    {
        $proxy = new SimpleObjectProxy('foo', 'bar');

        $realClassEventTriggered1 = false;
        $this->dispatcher->addListener('serializer.pre_serialize', function (PreSerializeEvent $event) use (&$realClassEventTriggered1) {
            $event->setType('foo', ['bar']);
        }, get_parent_class($proxy));

        $event = $this->createEvent($proxy, array('name' => get_class($proxy), 'params' => array()));
        $this->dispatcher->dispatch('serializer.pre_serialize', get_class($proxy), 'json', $event);

        $this->assertSame(['name' => 'foo', 'params' => ['bar']], $event->getType());
    }

    public function testListenersDoNotChangeTypeOnProxiesAndVirtualTypes()
    {
        $proxy = new SimpleObjectProxy('foo', 'bar');

        $event = $this->createEvent($proxy, ['name' => 'foo', 'params' => []]);
        $this->dispatcher->dispatch('serializer.pre_serialize', get_class($proxy), 'json', $event);

        $this->assertSame(['name' => 'foo', 'params' => []], $event->getType());
    }

    public function testOnPreSerializeMaintainsParams()
    {
        $object = new SimpleObjectProxy('foo', 'bar');
        $type = ['name' => SimpleObjectProxy::class, 'params' => ['baz']];

        $event = $this->createEvent($object, $type);
        $this->subscriber->onPreSerialize($event);

        $this->assertSame(['name' => SimpleObject::class, 'params' => ['baz']], $event->getType());
    }

    protected function setUp()
    {
        $this->subscriber = new DoctrineProxySubscriber();
        $this->visitor = $this->getMockBuilder('JMS\Serializer\Context')->getMock();

        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addSubscriber($this->subscriber);
    }

    private function createEvent($object, array $type)
    {
        return new PreSerializeEvent($this->visitor, $object, $type);
    }
}
