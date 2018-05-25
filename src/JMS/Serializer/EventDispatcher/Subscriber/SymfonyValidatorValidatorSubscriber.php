<?php

namespace JMS\Serializer\EventDispatcher\Subscriber;

use JMS\Serializer\EventDispatcher\Event;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\Exception\ValidationFailedException;
use PhpOption\None;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SymfonyValidatorValidatorSubscriber implements EventSubscriberInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.post_deserialize', 'method' => 'onPostDeserialize'),
        );
    }

    public function onPostDeserialize(Event $event)
    {
        $context = $event->getContext();

        if ($context->getDepth() > 0) {
            return;
        }

        $validator = $this->validator;
        $groups = $context->attributes->get('validation_groups') instanceof None
            ? null
            : $context->attributes->get('validation_groups')->get();

        if (!$groups) {
            return;
        }

        $constraints = $context->attributes->get('validation_constraints') instanceof None
            ? null
            : $context->attributes->get('validation_constraints')->get();

        $list = $validator->validate($event->getObject(), $constraints, $groups);

        if ($list->count() > 0) {
            throw new ValidationFailedException($list);
        }
    }
}
