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

namespace JMS\Serializer\EventDispatcher\Subscriber;

use Doctrine\Common\Persistence\Proxy;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
class DoctrineProxyTypeSubscriber implements EventSubscriberInterface
{
    public function onPreSerialize(PreSerializeEvent $event, $eventName, $class, $format, EventDispatcherInterface $dispatcher)
    {
        $object = $event->getObject();
        if ($object instanceof Proxy) {
            $parentClassName = get_parent_class($object);

            // check if this is already a re-dispatch
            if (strtolower($class) !== strtolower($parentClassName)) {
                $event->stopPropagation();
                $previousType = $event->getType();
                $newEvent = new PreSerializeEvent($event->getContext(), $object, array('name' => $parentClassName, 'params' => $previousType['params']));
                $dispatcher->dispatch($eventName, $parentClassName, $format, $newEvent);

                // update the type in case some listener changed it
                $newType = $newEvent->getType();
                $event->setType($newType['name'], $newType['params']);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize'),
        );
    }
}
