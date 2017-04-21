<?php

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

namespace JMS\Serializer\Tests\Serializer\EventDispatcher\Subscriber;

use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\EventDispatcher\Subscriber\DoctrineProxyTypeSubscriber;
use JMS\Serializer\Tests\Fixtures\SimpleObjectProxy;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
class DoctrineProxyTypeSubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    protected function setUp()
    {
        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addSubscriber(new DoctrineProxyTypeSubscriber());
    }

    public function testEventTriggeredOnRealClassName()
    {
        $proxy = new SimpleObjectProxy('foo', 'bar');

        $realClassEventTriggered1 = false;
        $this->dispatcher->addListener('serializer.pre_serialize', function () use (&$realClassEventTriggered1) {
            $realClassEventTriggered1 = true;
        }, get_parent_class($proxy));

        $event = new PreSerializeEvent($this->getMock('JMS\Serializer\Context'), $proxy, array('name' => get_class($proxy), 'params' => array()));
        $this->dispatcher->dispatch('serializer.pre_serialize', get_class($proxy), 'json', $event);

        $this->assertTrue($realClassEventTriggered1, "E1");
    }

    public function testListenersCanChangeType()
    {
        $proxy = new SimpleObjectProxy('foo', 'bar');

        $realClassEventTriggered1 = false;
        $this->dispatcher->addListener('serializer.pre_serialize', function (PreSerializeEvent $event) use (&$realClassEventTriggered1) {
            $event->setType('foo', ['bar']);
        }, get_parent_class($proxy));

        $event = new PreSerializeEvent($this->getMock('JMS\Serializer\Context'), $proxy, array('name' => get_class($proxy), 'params' => array()));
        $this->dispatcher->dispatch('serializer.pre_serialize', get_class($proxy), 'json', $event);

        $this->assertSame(['name' => 'foo', 'params' => ['bar']], $event->getType());
    }
}
