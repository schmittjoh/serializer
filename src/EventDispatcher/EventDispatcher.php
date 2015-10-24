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

namespace JMS\Serializer\EventDispatcher;
use JMS\Serializer\Exception\InvalidArgumentException;

/**
 * Light-weight event dispatcher.
 *
 * This implementation focuses primarily on performance, and dispatching
 * events for certain classes. It is not a general purpose event dispatcher.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class EventDispatcher implements EventDispatcherInterface
{
    private $listeners = array();
    private $classListeners = array();

    public static function getDefaultMethodName($eventName)
    {
        return 'on'.str_replace(array('_', '.'), '', $eventName);
    }

    /**
     * Sets the listeners.
     *
     * @param array $listeners
     */
    public function setListeners(array $listeners)
    {
        $this->listeners = $listeners;
        $this->classListeners = array();
    }

    public function addListener($eventName, $callable, $class = null, $format = null)
    {
        $this->listeners[$eventName][] = array($callable, null === $class ? null : strtolower($class), $format);
        unset($this->classListeners[$eventName]);
    }

    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventData) {
            if ( ! isset($eventData['event'])) {
                throw new InvalidArgumentException(sprintf('Each event must have a "event" key.'));
            }

            $method = isset($eventData['method']) ? $eventData['method'] : self::getDefaultMethodName($eventData['event']);
            $class = isset($eventData['class']) ? strtolower($eventData['class']) : null;
            $format = isset($eventData['format']) ? $eventData['format'] : null;
            $this->listeners[$eventData['event']][] = array(array($subscriber, $method), $class, $format);
            unset($this->classListeners[$eventData['event']]);
        }
    }

    public function hasListeners($eventName, $class, $format)
    {
        if ( ! isset($this->listeners[$eventName])) {
            return false;
        }

        $loweredClass = strtolower($class);
        if ( ! isset($this->classListeners[$eventName][$loweredClass][$format])) {
            $this->classListeners[$eventName][$loweredClass][$format] = $this->initializeListeners($eventName, $loweredClass, $format);
        }

        return !!$this->classListeners[$eventName][$loweredClass][$format];
    }

    public function dispatch($eventName, $class, $format, Event $event)
    {
        if ( ! isset($this->listeners[$eventName])) {
            return;
        }

        $loweredClass = strtolower($class);
        if ( ! isset($this->classListeners[$eventName][$loweredClass][$format])) {
            $this->classListeners[$eventName][$loweredClass][$format] = $this->initializeListeners($eventName, $loweredClass, $format);
        }

        foreach ($this->classListeners[$eventName][$loweredClass][$format] as $listener) {
            call_user_func($listener, $event);
        }
    }

    /**
     * @param string $eventName
     * @param string $loweredClass
     * @param string $format
     *
     * @return array An array of listeners
     */
    protected function initializeListeners($eventName, $loweredClass, $format)
    {
        $listeners = array();
        foreach ($this->listeners[$eventName] as $listener) {
            if (null !== $listener[1] && $loweredClass !== $listener[1]) {
                continue;
            }
            if (null !== $listener[2] && $format !== $listener[2]) {
                continue;
            }

            $listeners[] = $listener[0];
        }

        return $listeners;
    }
}
