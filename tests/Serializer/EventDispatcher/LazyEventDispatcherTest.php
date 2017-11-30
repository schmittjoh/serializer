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
