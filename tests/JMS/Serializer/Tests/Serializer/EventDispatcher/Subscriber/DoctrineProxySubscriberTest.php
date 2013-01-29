<?php

namespace JMS\Serializer\Tests\Serializer\EventDispatcher\Subscriber;

use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\EventDispatcher\Subscriber\DoctrineProxySubscriber;
use JMS\Serializer\Tests\Fixtures\SimpleObjectProxy;
use JMS\Serializer\VisitorInterface;

class DoctrineProxySubscriberTest extends \PHPUnit_Framework_TestCase
{
    /** @var VisitorInterface */
    private $visitor;

    /** @var DoctrineProxySubscriber */
    private $subscriber;

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

    protected function setUp()
    {
        $this->subscriber = new DoctrineProxySubscriber();
        $this->visitor = $this->getMock('JMS\Serializer\VisitorInterface');
    }

    private function createEvent($object, array $type)
    {
        return new PreSerializeEvent($this->visitor, $object, $type);
    }
}