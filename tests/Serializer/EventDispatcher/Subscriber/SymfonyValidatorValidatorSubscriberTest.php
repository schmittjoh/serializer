<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Serializer\EventDispatcher\Subscriber;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\Subscriber\SymfonyValidatorValidatorSubscriber;
use JMS\Serializer\Exception\ValidationFailedException;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Tests\Fixtures\AuthorList;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SymfonyValidatorValidatorSubscriberTest extends TestCase
{
    private $validator;

    /** @var SymfonyValidatorValidatorSubscriber */
    private $subscriber;

    public function testValidate(): void
    {
        $obj = new stdClass();

        $this->validator->expects(self::once())
            ->method('validate')
            ->with($obj, null, ['foo'])
            ->willReturn(new ConstraintViolationList());

        $context = DeserializationContext::create()->setAttribute('validation_groups', ['foo']);

        $this->subscriber->onPostDeserialize(new ObjectEvent($context, $obj, []));
    }

    public function testValidateThrowsExceptionWhenListIsNotEmpty(): void
    {
        $obj = new stdClass();

        $this->validator->expects(self::once())
            ->method('validate')
            ->with($obj, null, ['foo'])
            ->willReturn(new ConstraintViolationList([new ConstraintViolation('foo', 'foo', [], 'a', 'b', 'c')]));

        $context = DeserializationContext::create()->setAttribute('validation_groups', ['foo']);

        $this->expectException(ValidationFailedException::class);
        $this->expectExceptionMessage('Validation failed with 1 error(s).');

        $this->subscriber->onPostDeserialize(new ObjectEvent($context, $obj, []));
    }

    public function testValidatorIsNotCalledWhenNoGroupsAreSet(): void
    {
        $this->validator->expects(self::never())
            ->method('validate');

        $this->subscriber->onPostDeserialize(new ObjectEvent(DeserializationContext::create(), new stdClass(), []));
    }

    public function testValidationIsOnlyPerformedOnRootObject(): void
    {
        $this->validator->expects(self::once())
            ->method('validate')
            ->with(self::isInstanceOf(AuthorList::class), null, ['Foo'])
            ->willReturn(new ConstraintViolationList());

        $subscriber = $this->subscriber;
        $list = SerializerBuilder::create()
            ->configureListeners(static function (EventDispatcher $dispatcher) use ($subscriber) {
                $dispatcher->addSubscriber($subscriber);
            })
            ->build()
            ->deserialize(
                '{"authors":[{"full_name":"foo"},{"full_name":"bar"}]}',
                AuthorList::class,
                'json',
                DeserializationContext::create()->setAttribute('validation_groups', ['Foo'])
            );

        self::assertCount(2, $list);
    }

    protected function setUp(): void
    {
        $this->validator = $this->getMockBuilder(ValidatorInterface::class)->getMock();
        $this->subscriber = new SymfonyValidatorValidatorSubscriber($this->validator);
    }
}
