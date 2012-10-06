<?php

namespace JMS\SerializerBundle\EventDispatcher;

interface EventDispatcherInterface
{
    /**
     * Returns whether there are listeners.
     *
     * @param string $eventName
     * @param string $class
     * @param string $format
     *
     * @return boolean
     */
    public function hasListeners($eventName, $class, $format);

    /**
     * Dispatches an event.
     *
     * The listeners/subscribers are called in the same order in which they
     * were added to the dispatcher.
     *
     * @param string $eventName
     * @param string $class
     * @param string $format
     * @param Event $event
     */
    public function dispatch($eventName, $class, $format, Event $event);

    /**
     * Adds a listener.
     *
     * @param string $eventName
     * @param callable $callable
     * @param string|null $class
     * @param string|null $format
     */
    public function addListener($eventName, $callable, $class = null, $format = null);

    /**
     * Adds a subscribers.
     *
     * @param EventSubscriberInterface $subscriber
     */
    public function addSubscriber(EventSubscriberInterface $subscriber);
}