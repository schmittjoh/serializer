<?php

namespace JMS\SerializerBundle\DependencyInjection\Compiler;

use JMS\SerializerBundle\EventDispatcher\EventDispatcher;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class RegisterEventListenersAndSubscribersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $listeners = array();
        foreach ($container->findTaggedServiceIds('jms_serializer.event_listener') as $id => $tags) {
            foreach ($tags as $attributes) {
                if ( ! isset($attributes['event'])) {
                    throw new \RuntimeException(sprintf('The tag "jms_serializer.event_listener" of service "%s" requires an attribute named "event".', $id));
                }

                $class = isset($attributes['class']) ? $attributes['class'] : null;
                $format = isset($attributes['format']) ? $attributes['format'] : null;
                $method = isset($attributes['method']) ? $attributes['method'] : EventDispatcher::getDefaultMethodName($attributes['event']);
                $priority = isset($attributes['priority']) ? (integer) $attributes['priority'] : 0;

                $listeners[$priority][] = array($attributes['event'], array($id, $method), $class, $format);
            }
        }

        foreach ($container->findTaggedServiceIds('jms_serializer.event_subscriber') as $id => $tags) {
            $subscriberClass = $container->getDefinition($id)->getClass();
            if ( ! is_subclass_of($subscriberClass, 'JMS\SerializerBundle\EventDispatcher\EventSubscriberInterface')) {
                throw new \RuntimeException(sprintf('The service "%s" (class: %s) does not implement the EventSubscriberInterface.', $id, $subscriberClass));
            }

            foreach (call_user_func($subscriberClass, 'getSubscribedEvents') as $eventData) {
                if ( ! isset($eventData['event'])) {
                    throw new \RuntimeException(sprintf('The service "%s" (class: %s) must return an event for each subscribed event.', $id, $subscriberClass));
                }

                $class = isset($eventData['class']) ? $eventData['class'] : null;
                $format = isset($eventData['format']) ? $eventData['format'] : null;
                $method = isset($eventData['method']) ? $eventData['method'] : EventDispatcher::getDefaultMethodName($eventData['event']);
                $priority = isset($attributes['priority']) ? (integer) $attributes['priority'] : 0;

                $listeners[$priority][] = array($eventData['event'], array($id, $method), $class, $format);
            }
        }

        if ($listeners) {
            ksort($listeners);

            $container->getDefinition('jms_serializer.event_dispatcher')
                ->addMethodCall('setListeners', array(call_user_func_array('array_merge', $listeners)));
        }
    }
}
