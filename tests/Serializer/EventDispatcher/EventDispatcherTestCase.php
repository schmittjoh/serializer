<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Serializer\EventDispatcher;

use Doctrine\Persistence\Proxy;
use JMS\Serializer\Context;
use JMS\Serializer\EventDispatcher\Event;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\Tests\Fixtures\SimpleObject;
use JMS\Serializer\Tests\Fixtures\SimpleObjectProxy;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class EventDispatcherTestCase extends TestCase
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
        $this->dispatcher->addListener('foo', static function () {
        });
        self::assertTrue($this->dispatcher->hasListeners('foo', 'Foo', 'json'));

        self::assertFalse($this->dispatcher->hasListeners('bar', 'Bar', 'json'));
        $this->dispatcher->addListener('bar', static function () {
        }, 'Foo');
        self::assertFalse($this->dispatcher->hasListeners('bar', 'Bar', 'json'));
        $this->dispatcher->addListener('bar', static function () {
        }, 'Bar', 'xml');
        self::assertFalse($this->dispatcher->hasListeners('bar', 'Bar', 'json'));
        $this->dispatcher->addListener('bar', static function () {
        }, null, 'json');
        self::assertTrue($this->dispatcher->hasListeners('bar', 'Baz', 'json'));
        self::assertTrue($this->dispatcher->hasListeners('bar', 'Bar', 'json'));

        self::assertFalse($this->dispatcher->hasListeners('baz', 'Bar', 'xml'));
        $this->dispatcher->addListener('baz', static function () {
        }, 'Bar');
        self::assertTrue($this->dispatcher->hasListeners('baz', 'Bar', 'xml'));
        //self::assertTrue($this->dispatcher->hasListeners('baz', 'bAr', 'xml'));
    }

    public function testListenersAreOrderedByPriority()
    {
        $calls = [];
        $this->dispatcher->addListener('foo', static function () use (&$calls) {
            $calls[] = 'default';
        });
        $this->dispatcher->addListener('foo', static function () use (&$calls) {
            $calls[] = 'low';
        }, null, null, null, -10);
        $this->dispatcher->addListener('foo', static function () use (&$calls) {
            $calls[] = 'high';
        }, null, null, null, 10);

        $this->dispatch('foo');

        self::assertSame(['high', 'default', 'low'], $calls);
    }

    public function testSubscriberPriority()
    {
        $calls = [];
        $this->dispatcher->addSubscriber(new class ($calls) implements EventSubscriberInterface {
            private $calls;

            public function __construct(array &$calls)
            {
                $this->calls = &$calls;
            }

            public static function getSubscribedEvents(): array
            {
                return [
                    ['event' => 'foo', 'method' => 'first'],
                    ['event' => 'foo', 'method' => 'second', 'priority' => 5],
                ];
            }

            public function first()
            {
                $this->calls[] = 'first';
            }

            public function second()
            {
                $this->calls[] = 'second';
            }
        });

        $this->dispatch('foo');

        self::assertSame(['second', 'first'], $calls);
    }

    public function testPriorityRespectsClassAndFormatFilters()
    {
        $calls = [];
        $this->dispatcher->addListener('foo', static function () use (&$calls) {
            $calls[] = 'any';
        });
        $this->dispatcher->addListener('foo', static function () use (&$calls) {
            $calls[] = 'xml';
        }, null, 'xml', null, 10);
        $this->dispatcher->addListener('foo', static function () use (&$calls) {
            $calls[] = 'json';
        }, null, 'json', null, 1);

        $this->dispatch('foo');

        self::assertSame(['json', 'any'], $calls);
    }

    public function testListenersWithoutPriorityElementAreSupported()
    {
        $calls = [];
        $this->dispatcher->setListeners([
            'foo' => [
                [
                    static function () use (&$calls) {
                        $calls[] = 'legacy';
                    },
                    null,
                    null,
                    null,
                ],
            ],
        ]);
        $this->dispatcher->addListener('foo', static function () use (&$calls) {
            $calls[] = 'prioritized';
        }, null, null, null, 1);

        $this->dispatch('foo');

        self::assertSame(['prioritized', 'legacy'], $calls);
    }

    public function testDispatch()
    {
        $a = new MockListener();
        $this->dispatcher->addListener('foo', [$a, 'Foo']);
        $this->dispatch('bar');
        $a->verify('Listener is not called for other event.');

        $b = new MockListener();
        $this->dispatcher->addListener('pre', [$b, 'bar'], 'Bar');
        $this->dispatcher->addListener('pre', [$b, 'foo'], 'Foo');
        $this->dispatcher->addListener('pre', [$b, 'all']);

        $b->bar($this->event, 'pre', 'Bar', 'json', $this->dispatcher);
        $b->all($this->event, 'pre', 'Bar', 'json', $this->dispatcher);
        $b->foo($this->event, 'pre', 'Foo', 'json', $this->dispatcher);
        $b->all($this->event, 'pre', 'Foo', 'json', $this->dispatcher);

        $b->replay();
        $this->dispatch('pre', 'Bar');
        $this->dispatch('pre', 'Foo');
        $b->verify();
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

        $a->replay();
        $this->dispatch('pre', 'Bar', 'json', $event);
        $a->verify();
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

        $a->replay();
        $this->dispatch('pre', 'Bar', 'json', $event);
        $a->verify();
    }

    public function testListenerCanStopPropagation()
    {
        $listener1 = false;
        $listener2 = false;

        $this->dispatcher->addListener('pre', static function (Event $event) use (&$listener1) {
            $event->stopPropagation();
            $listener1 = true;
        });

        $this->dispatcher->addListener('pre', static function () use (&$listener2) {
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

        $this->dispatcher->addListener('pre', static function (Event $event, $eventName, $loweredClass, $format, EventDispatcherInterface $dispatcher) use (&$listener1) {
            $listener1 = true;

            $event = new Event($event->getContext(), $event->getType());

            self::assertSame('pre', $eventName);
            self::assertSame('json', $format);
            self::assertSame('Foo', $loweredClass);

            $dispatcher->dispatch('post', 'Blah', 'xml', $event);
        });

        $this->dispatcher->addListener('pre', static function () use (&$listener2) {
            $listener2 = true;
        });

        $this->dispatcher->addListener('post', static function (Event $event, $eventName, $loweredClass, $format, EventDispatcherInterface $dispatcher) use (&$listener3) {
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

        $listenersReflection = new \ReflectionProperty(EventDispatcher::class, 'listeners');
        if (\PHP_VERSION_ID < 80100) {
            $listenersReflection->setAccessible(true);
        }

        self::assertSame([
            'foo.bar_baz' => [
                [[$subscriber, 'onfoobarbaz'], null, 'foo', null, 0],
            ],
            'bar' => [
                [[$subscriber, 'bar'], 'foo', null, null, 0],
            ],
        ], $listenersReflection->getValue($this->dispatcher));
    }

    protected function setUp(): void
    {
        $this->context = $this->getMockBuilder(Context::class)->getMock();

        $this->dispatcher = $this->createEventDispatcher();
        $this->event = new ObjectEvent($this->context, new \stdClass(), ['name' => 'foo', 'params' => []]);
    }

    protected function createEventDispatcher()
    {
        return new EventDispatcher();
    }

    protected function dispatch($eventName, $class = 'Foo', $format = 'json', ?Event $event = null)
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

/**
 * @method bar(...$args)
 * @method all(...$args)
 * @method foo(...$args)
 * @method onlyProxy(...$args)
 */
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

    public function replay()
    {
        $this->wasReplayed = true;
    }

    public function verify($message = '')
    {
        Assert::assertSame($this->expected, $this->actual, $message);
    }
}
