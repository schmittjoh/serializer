<?php

namespace JMS\Serializer\EventDispatcher\Subscriber;

use JMS\Serializer\EventDispatcher\Event;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\Exception\ValidationFailedException;
use Symfony\Component\Validator\ValidatorInterface;

class SymfonyValidatorSubscriber implements EventSubscriberInterface
{
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
        $context->attributes->get('validation_groups')->map(
            function (array $groups) use ($event, $validator) {
                $list = $validator->validate($event->getObject(), $groups);

                if ($list->count() > 0) {
                    throw new ValidationFailedException($list);
                }
            }
        );
    }
}
