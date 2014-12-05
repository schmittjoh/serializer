<?php

/*
 * Copyright 2013 Johannes M. Schmitt <schmittjoh@gmail.com>
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

use JMS\Serializer\EventDispatcher\Event;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\Tests\Fixtures\Author;
use JMS\Serializer\Tests\Fixtures\AuthorList;

class NestedSerializationTest extends BaseSerializationTest
{
    protected function setUp()
    {
        parent::setup();
        $this->dispatcher->addSubscriber(new CallingSerializerSubscriber($this->serializer));
    }

    public function testSerializerImbrication()
    {
        $list = new AuthorList();
        $list->add(new Author('foo'));
        $list->add(new Author('bar'));

        $this->assertEquals('{"authors":[{"full_name":"foo"},{"full_name":"bar"}],"_extraJson":"{\"foo\":\"bar\"}"}', $this->serializer->serialize($list, 'json'));
    }

    protected function getContent($key)
    {
        return [];
    }

    protected function getFormat()
    {
        return 'json';
    }
}

class CallingSerializerSubscriber implements EventSubscriberInterface
{
    protected $serializer;

    public function __construct($serializer)
    {
        $this->serializer = $serializer;
    }

    public function onPostSerialize(Event $event)
    {
        // could be to add data, or log, send message etc...
        $event->getVisitor()->addData('_extraJson', $this->serializer->serialize(array('foo' => 'bar'), 'json'));
    }

    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.post_serialize', 'method' => 'onPostSerialize', 'format' => 'json', 'class' => 'JMS\Serializer\Tests\Fixtures\AuthorList'),
        );
    }
}
