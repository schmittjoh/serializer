<?php

declare(strict_types=1);

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

namespace JMS\Serializer\Tests\Serializer\EventDispatcher;

use Doctrine\Common\Persistence\Proxy;
use JMS\Serializer\Context;
use JMS\Serializer\EventDispatcher\Event;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\Tests\Fixtures\SimpleObject;
use JMS\Serializer\Tests\Fixtures\SimpleObjectProxy;
use PHPUnit\Framework\Assert;

class EventDispatcherTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var EventDispatcher
     */
    protected $dispatcher;
    protected $event;
    protected $context;

    public function testHasListeners()
    {
        self::assertFalse($this->dispatcher->hasListeners('foo', 'Foo', 'json'));
        $this->dispatcher->addListener('foo', function () {
        });
        self::assertTrue($this->dispatcher->hasListeners('foo', 'Foo', 'json'));

        self::assertFalse($this->dispatcher->hasListeners('bar', 'Bar', 'json'));
        $this->dispatcher->addListener('bar', function () {
        }, 'Foo');
        self::assertFalse($this->dispatcher->hasListeners('bar', 'Bar', 'json'));
        $this->dispatcher->addListener('bar', function () {
        }, 'Bar', 'xml');
        self::assertFalse($this->dispatcher->hasListeners('bar', 'Bar', 'json'));
        $this->dispatcher->addListener('bar', function () {
        }, null, 'json');
        self::assertTrue($this->dispatcher->hasListeners('bar', 'Baz', 'json'));
        self::assertTrue($this->dispatcher->hasListeners('bar', 'Bar', 'json'));

        self::assertFalse($this->dispatcher->hasListeners('baz', 'Bar', 'xml'));
        $this->dispatcher->addListener('baz', function () {
        }, 'Bar');
        self::assertTrue($this->dispatcher->hasListeners('baz', 'Bar', 'xml'));
        //self::assertTrue($this->dispatcher->hasListeners('baz', 'bAr', 'xml'));
    }

    public function testDispatch()
    {
        $a = new MockListener();
        $this->dispatcher->addListener('foo', [$a, 'Foo']);
        $this->dispatch('bar');
        $a->_verify('Listener is not called for other event.');

        $b = new MockListener();
        $this->dispatcher->addListener('pre', [$b, 'bar'], 'Bar');
        $this->dispatcher->addListener('pre', [$b, 'foo'], 'Foo');
        $this->dispatcher->addListener('pre', [$b, 'all']);

        $b->bar($this->event, 'pre', 'Bar', 'json', $this->dispatcher);
        $b->all($this->event, 'pre', 'Bar', 'json', $this->dispatcher);
        $b->foo($this->event, 'pre', 'Foo', 'json', $this->dispatcher);
        $b->all($this->event, 'pre', 'Foo', 'json', $this->dispatcher);

        $b->_replay();
        $this->dispatch('pre', 'Bar');
        $this->dispatch('pre', 'Foo');
        $b->_verify();
    }

    public function testDispatchWithInstanceFilteringBothListenersInvoked()
    {
        $a = new MockListener();

        $this->dispatcher->addListener('pre', [$a, 'onlyProxy'], 'Bar', 'json', Proxy::class);
        $this->dispatcher->addListener('pre', [$a, 'all'], 'Bar', 'json');

        $object = new SimpleObjectProxy('a', 'b');
        $event = new ObjectEvent($this->context, $object, ['name' => 'foo', 'params' => []]);

        // expected
        $a->onlyProxy($event, 'pre', 'Bar', 'json', $this->dispatcher);
        $a->all($event, 'pre', 'Bar', 'json', $this->dispatcher);

        $a->_replay();
        $this->dispatch('pre', 'Bar', 'json', $event);
        $a->_verify();
    }

    public function testDispatchWithInstanceFilteringOnlyGenericListenerInvoked()
    {
        $a = new MockListener();

        $this->dispatcher->addListener('pre', [$a, 'onlyProxy'], 'Bar', 'json', Proxy::class);
        $this->dispatcher->addListener('pre', [$a, 'all'], 'Bar', 'json');

        $object = new SimpleObject('a', 'b');
        $event = new ObjectEvent($this->context, $object, ['name' => 'foo', 'params' => []]);

        // expected
        $a->all($event, 'pre', 'Bar', 'json', $this->dispatcher);

        $a->_replay();
        $this->dispatch('pre', 'Bar', 'json', $event);
        $a->_verify();
    }

    public function testListenerCanStopPropagation()
    {
        $listener1 = false;
        $listener2 = false;

        $this->dispatcher->addListener('pre', function (Event $event) use (&$listener1) {
            $event->stopPropagation();
            $listener1 = true;
        });

        $this->dispatcher->addListener('pre', function () use (&$listener2) {
            $listener2 = true;
        });

        $this->dispatch('pre');

        self::assertTrue($listener1);
        self::assertFalse($listener2);
    }

    public function testListenerCanDispatchEvent()
    {
        $listener1 = false;
        $listener2 = false;
        $listener3 = false;

        $this->dispatcher->addListener('pre', function (Event $event, $eventName, $loweredClass, $format, EventDispatcherInterface $dispatcher) use (&$listener1) {
            $listener1 = true;

            $event = new Event($event->getContext(), $event->getType());

            self::assertSame('pre', $eventName);
            self::assertSame('json', $format);
            self::assertSame('Foo', $loweredClass);

            $dispatcher->dispatch('post', 'Blah', 'xml', $event);
        });

        $this->dispatcher->addListener('pre', function () use (&$listener2) {
            $listener2 = true;
        });

        $this->dispatcher->addListener('post', function (Event $event, $eventName, $loweredClass, $format, EventDispatcherInterface $dispatcher) use (&$listener3) {
            $listener3 = true;

            self::assertSame('post', $eventName);
            self::assertSame('xml', $format);
            self::assertSame('Blah', $loweredClass);
        });

        $this->dispatch('pre');

        self::assertTrue($listener1);
        self::assertTrue($listener2);
        self::assertTrue($listener3);
    }

    public function testAddSubscriber()
    {
        $subscriber = new MockSubscriber();
        MockSubscriber::$events = [
            ['event' => 'foo.bar_baz', 'format' => 'foo'],
            ['event' => 'bar', 'method' => 'bar', 'class' => 'foo'],
        ];

        $this->dispatcher->addSubscriber($subscriber);
        self::assertAttributeEquals([
            'foo.bar_baz' => [
                [[$subscriber, 'onfoobarbaz'], null, 'foo', null],
            ],
            'bar' => [
                [[$subscriber, 'bar'], 'foo', null, null],
            ],
        ], 'listeners', $this->dispatcher);
    }

    protected function setUp()
    {
        $this->context = $this->getMockBuilder(Context::class)->getMock();

        $this->dispatcher = $this->createEventDispatcher();
        $this->event = new ObjectEvent($this->context, new \stdClass(), ['name' => 'foo', 'params' => []]);
    }

    protected function createEventDispatcher()
    {
        return new EventDispatcher();
    }

    protected function dispatch($eventName, $class = 'Foo', $format = 'json', Event $event = null)
    {
        $this->dispatcher->dispatch($eventName, $class, $format, $event ?: $this->event);
    }
}

class MockSubscriber implements EventSubscriberInterface
{
    public static $events = [];

    public static function getSubscribedEvents()
    {
        return self::$events;
    }
}

class MockListener
{
    private $expected = [];
    private $actual = [];
    private $wasReplayed = false;

    public function __call($method, array $args = [])
    {
        if (!$this->wasReplayed) {
            $this->expected[] = [$method, $args];

            return;
        }

        $this->actual[] = [$method, $args];
    }

    public function _replay()
    {
        $this->wasReplayed = true;
    }

    public function _verify($message = '')
    {
        Assert::assertSame($this->expected, $this->actual, $message);
    }
}
