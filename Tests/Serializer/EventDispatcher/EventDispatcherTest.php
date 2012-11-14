<?php

namespace JMS\SerializerBundle\Tests\Serializer\EventDispatcher;

use JMS\SerializerBundle\Serializer\EventDispatcher\Event;
use JMS\SerializerBundle\Serializer\EventDispatcher\EventDispatcher;
use JMS\SerializerBundle\Serializer\EventDispatcher\EventSubscriberInterface;

class EventDispatcherTest extends \PHPUnit_Framework_TestCase
{
    private $dispatcher;
    private $event;

    public function testHasListeners()
    {
        $this->assertFalse($this->dispatcher->hasListeners('foo', 'Foo', 'json'));
        $this->dispatcher->addListener('foo', function() { });
        $this->assertTrue($this->dispatcher->hasListeners('foo', 'Foo', 'json'));

        $this->assertFalse($this->dispatcher->hasListeners('bar', 'Bar', 'json'));
        $this->dispatcher->addListener('bar', function() { }, 'Foo');
        $this->assertFalse($this->dispatcher->hasListeners('bar', 'Bar', 'json'));
        $this->dispatcher->addListener('bar', function() { }, 'Bar', 'xml');
        $this->assertFalse($this->dispatcher->hasListeners('bar', 'Bar', 'json'));
        $this->dispatcher->addListener('bar', function() { }, null, 'json');
        $this->assertTrue($this->dispatcher->hasListeners('bar', 'Baz', 'json'));
        $this->assertTrue($this->dispatcher->hasListeners('bar', 'Bar', 'json'));

        $this->assertFalse($this->dispatcher->hasListeners('baz', 'Bar', 'xml'));
        $this->dispatcher->addListener('baz', function() { }, 'Bar');
        $this->assertTrue($this->dispatcher->hasListeners('baz', 'Bar', 'xml'));
        $this->assertTrue($this->dispatcher->hasListeners('baz', 'bAr', 'xml'));
    }

    public function testDispatch()
    {
        $a = new MockListener();
        $this->dispatcher->addListener('foo', array($a, 'foo'));
        $this->dispatch('bar');
        $a->_verify('Listener is not called for other event.');

        $b = new MockListener();
        $this->dispatcher->addListener('pre', array($b, 'bar'), 'Bar');
        $this->dispatcher->addListener('pre', array($b, 'foo'), 'Foo');
        $this->dispatcher->addListener('pre', array($b, 'all'));

        $b->bar($this->event);
        $b->all($this->event);
        $b->foo($this->event);
        $b->all($this->event);
        $b->_replay();
        $this->dispatch('pre', 'Bar');
        $this->dispatch('pre', 'Foo');
        $b->_verify();
    }

    public function testAddSubscriber()
    {
        $subscriber = new MockSubscriber();
        $subscriber::$events = array(
            array('event' => 'foo.bar_baz', 'format' => 'foo'),
            array('event' => 'bar', 'method' => 'bar', 'class' => 'foo'),
        );

        $this->dispatcher->addSubscriber($subscriber);
        $this->assertAttributeEquals(array(
            'foo.bar_baz' => array(
                array(array($subscriber, 'onfoobarbaz'), null, 'foo'),
            ),
            'bar' => array(
                array(array($subscriber, 'bar'), 'foo', null),
            ),
        ), 'listeners', $this->dispatcher);
    }

    protected function setUp()
    {
        $this->dispatcher = new EventDispatcher();
        $this->event = new Event($this->getMock('JMS\SerializerBundle\Serializer\VisitorInterface'), new \stdClass(), array('name' => 'foo', 'params' => array()));
    }

    private function dispatch($eventName, $class = 'Foo', $format = 'json', Event $event = null)
    {
        $this->dispatcher->dispatch($eventName, $class, $format, $event ?: $this->event);
    }
}

class MockSubscriber implements EventSubscriberInterface
{
    public static $events = array();

    public static function getSubscribedEvents()
    {
        return self::$events;
    }
}

class MockListener
{
    private $expected = array();
    private $actual = array();
    private $wasReplayed = false;

    public function __call($method, array $args = array())
    {
        if ( ! $this->wasReplayed) {
            $this->expected[] = array($method, $args);

            return;
        }

        $this->actual[] = array($method, $args);
    }

    public function _replay()
    {
        $this->wasReplayed = true;
    }

    public function _verify($message = null)
    {
        \PHPUnit_Framework_Assert::assertSame($this->expected, $this->actual, $message);
    }
}