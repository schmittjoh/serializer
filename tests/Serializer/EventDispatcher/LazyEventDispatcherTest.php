<?php

namespace JMS\Serializer\Tests\Serializer\EventDispatcher;

use JMS\Serializer\EventDispatcher\LazyEventDispatcher;

abstract class LazyEventDispatcherTest extends EventDispatcherTest
{
    protected $container;

    protected function setUp()
    {
        $this->container = $this->createContainer();

        parent::setUp();
    }

    public function testHasListenersWithListenerAsService()
    {
        $a = new MockListener();
        $this->registerListenerService('a', $a);

        $this->assertFalse($this->dispatcher->hasListeners('foo', 'Foo', 'json'));
        $this->dispatcher->addListener('foo', ['a', 'foo']);
        $this->assertTrue($this->dispatcher->hasListeners('foo', 'Foo', 'json'));
    }

    public function testDispatchWithListenerAsService()
    {
        $a = new MockListener();
        $this->registerListenerService('a', $a);

        $this->dispatcher->addListener('foo', ['a', 'foo']);
        $this->dispatch('bar');
        $a->_verify('Listener is not called for other event.');

        $b = new MockListener();
        $this->registerListenerService('b', $b);

        $this->dispatcher->addListener('pre', array('b', 'bar'), 'Bar');
        $this->dispatcher->addListener('pre', array('b', 'foo'), 'Foo');
        $this->dispatcher->addListener('pre', array('b', 'all'));

        $b->bar($this->event, 'pre', 'bar', 'json', $this->dispatcher);
        $b->all($this->event, 'pre', 'bar', 'json', $this->dispatcher);
        $b->foo($this->event, 'pre', 'foo', 'json', $this->dispatcher);
        $b->all($this->event, 'pre', 'foo', 'json', $this->dispatcher);
        $b->_replay();
        $this->dispatch('pre', 'Bar');
        $this->dispatch('pre', 'Foo');
        $b->_verify();
    }

    protected function createEventDispatcher()
    {
        return new LazyEventDispatcher($this->container);
    }

    abstract protected function createContainer();

    abstract protected function registerListenerService($serviceId, MockListener $listener);
}
