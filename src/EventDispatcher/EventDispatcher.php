<?php

declare(strict_types=1);

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
    /**
     * @var array
     */
    private $listeners = [];

    /**
     * ClassListeners cache
     *
     * @var array
     */
    private $classListeners = [];

    public static function getDefaultMethodName(string $eventName): string
    {
        return 'on' . str_replace(['_', '.'], '', $eventName);
    }

    /**
     * Sets the listeners.
     *
     * @param array $listeners
     */
    public function setListeners(array $listeners): void
    {
        $this->listeners = $listeners;
        $this->classListeners = [];
    }

    /**
     * {@inheritdoc}
     */
    public function addListener(string $eventName, $callable, ?string $class = null, ?string $format = null, ?string $interface = null): void
    {
        $this->listeners[$eventName][] = [$callable, $class, $format, $interface];
        unset($this->classListeners[$eventName]);
    }

    public function addSubscriber(EventSubscriberInterface $subscriber): void
    {
        foreach ($subscriber->getSubscribedEvents() as $eventData) {
            if (!isset($eventData['event'])) {
                throw new InvalidArgumentException(sprintf('Each event must have a "event" key.'));
            }

            $method = $eventData['method'] ?? self::getDefaultMethodName($eventData['event']);
            $class = $eventData['class'] ?? null;
            $format = $eventData['format'] ?? null;
            $interface = $eventData['interface'] ?? null;
            $this->listeners[$eventData['event']][] = [[$subscriber, $method], $class, $format, $interface];
            unset($this->classListeners[$eventData['event']]);
        }
    }

    public function hasListeners(string $eventName, string $class, string $format): bool
    {
        if (!isset($this->listeners[$eventName])) {
            return false;
        }

        if (!isset($this->classListeners[$eventName][$class][$format])) {
            $this->classListeners[$eventName][$class][$format] = $this->initializeListeners($eventName, $class, $format);
        }

        return !!$this->classListeners[$eventName][$class][$format];
    }

    public function dispatch(string $eventName, string $class, string $format, Event $event): void
    {
        if (!isset($this->listeners[$eventName])) {
            return;
        }

        $object = $event instanceof ObjectEvent ? $event->getObject() : null;
        $realClass = is_object($object) ? get_class($object) : '';
        $objectClass = $realClass !== $class ? $realClass . $class : $class;

        if (!isset($this->classListeners[$eventName][$objectClass][$format])) {
            $this->classListeners[$eventName][$objectClass][$format] = $this->initializeListeners($eventName, $class, $format);
        }

        foreach ($this->classListeners[$eventName][$objectClass][$format] as $listener) {
            if (!empty($listener[3]) && !($object instanceof $listener[3])) {
                continue;
            }

            \call_user_func($listener[0], $event, $eventName, $class, $format, $this);

            if ($event->isPropagationStopped()) {
                break;
            }
        }
    }

    /**
     * @return array An array of listeners
     */
    protected function initializeListeners(string $eventName, string $loweredClass, string $format): array
    {
        $listeners = [];
        foreach ($this->listeners[$eventName] as $listener) {
            if (null !== $listener[1] && $loweredClass !== $listener[1]) {
                continue;
            }

            if (null !== $listener[2] && $format !== $listener[2]) {
                continue;
            }

            $listeners[] = $listener;
        }

        return $listeners;
    }
}
