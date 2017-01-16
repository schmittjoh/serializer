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

use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\Subscriber\SymfonyValidatorSubscriber;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Tests\Fixtures\AuthorList;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class SymfonyValidatorSubscriberTest extends \PHPUnit_Framework_TestCase
{
    private $validator;

    /** @var SymfonyValidatorSubscriber */
    private $subscriber;

    public function testValidate()
    {
        $obj = new \stdClass;

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($obj, array('foo'))
            ->will($this->returnValue(new ConstraintViolationList()));

        $context = DeserializationContext::create()->setAttribute('validation_groups', array('foo'));

        $this->subscriber->onPostDeserialize(new ObjectEvent($context, $obj, array()));
    }

    /**
     * @expectedException JMS\Serializer\Exception\ValidationFailedException
     * @expectedExceptionMessage Validation failed with 1 error(s).
     */
    public function testValidateThrowsExceptionWhenListIsNotEmpty()
    {
        $obj = new \stdClass;

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($obj, array('foo'))
            ->will($this->returnValue(new ConstraintViolationList(array(new ConstraintViolation('foo', 'foo', array(), 'a', 'b', 'c')))));

        $context = DeserializationContext::create()->setAttribute('validation_groups', array('foo'));

        $this->subscriber->onPostDeserialize(new ObjectEvent($context, $obj, array()));
    }

    public function testValidatorIsNotCalledWhenNoGroupsAreSet()
    {
        $this->validator->expects($this->never())
            ->method('validate');

        $this->subscriber->onPostDeserialize(new ObjectEvent(DeserializationContext::create(), new \stdClass, array()));
    }

    public function testValidationIsOnlyPerformedOnRootObject()
    {
        $this->validator->expects($this->once())
            ->method('validate')
            ->with($this->isInstanceOf('JMS\Serializer\Tests\Fixtures\AuthorList'), array('Foo'))
            ->will($this->returnValue(new ConstraintViolationList()));

        $subscriber = $this->subscriber;
        $list = SerializerBuilder::create()
            ->configureListeners(function(EventDispatcher $dispatcher) use ($subscriber) {
                $dispatcher->addSubscriber($subscriber);
            })
            ->build()
            ->deserialize(
                '{"authors":[{"full_name":"foo"},{"full_name":"bar"}]}',
                'JMS\Serializer\Tests\Fixtures\AuthorList',
                'json',
                DeserializationContext::create()->setAttribute('validation_groups', array('Foo'))
            );

        $this->assertCount(2, $list);
    }

    protected function setUp()
    {
        if (!interface_exists('Symfony\Component\Validator\ValidatorInterface')) {
            $this->markTestSkipped('Symfony\Component\Validator\ValidatorInterface is not available');
        }

        $this->validator = $this->getMock('Symfony\Component\Validator\ValidatorInterface');
        $this->subscriber = new SymfonyValidatorSubscriber($this->validator);
    }
}
