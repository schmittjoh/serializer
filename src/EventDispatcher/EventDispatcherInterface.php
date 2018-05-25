<?php

declare(strict_types=1);

namespace JMS\Serializer\EventDispatcher;

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
    public function hasListeners(string $eventName, string $class, string $format): bool;

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
     * @return void
     */
    public function dispatch($eventName, string $class, string $format, Event $event): void;

    /**
     * Adds a listener.
     *
     * @param string $eventName
     * @param callable $callable
     * @param string|null $class
     * @param string|null $format
     * @param string|null $interface
     * @return void
     */
    public function addListener(string $eventName, $callable, ?string $class = null, ?string $format = null, ?string $interface = null): void;

    /**
     * Adds a subscribers.
     *
     * @param EventSubscriberInterface $subscriber
     * @return void
     */
    public function addSubscriber(EventSubscriberInterface $subscriber): void;
}
