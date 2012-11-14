<?php

namespace JMS\SerializerBundle\DependencyInjection\Compiler;

use JMS\SerializerBundle\Serializer\EventDispatcher\EventDispatcher;
use JMS\SerializerBundle\Serializer\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class RegisterEventListenersAndSubscribersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $listeners = array();
        foreach ($container->findTaggedServiceIds('jms_serializer.event_listener') as $id => $tags) {
            if (!$container->getDefinition($id)->isPublic()) {
                throw new \RuntimeException(sprintf('The tag "jms_serializer.event_listener" of service "%s" requires the service to be public.', $id));
            }

            foreach ($tags as $attributes) {
                if ( ! isset($attributes['event'])) {
                    throw new \RuntimeException(sprintf('The tag "jms_serializer.event_listener" of service "%s" requires an attribute named "event".', $id));
                }

                $class = isset($attributes['class']) ? strtolower($attributes['class']) : null;
                $format = isset($attributes['format']) ? $attributes['format'] : null;
                $method = isset($attributes['method']) ? $attributes['method'] : EventDispatcher::getDefaultMethodName($attributes['event']);
                $priority = isset($attributes['priority']) ? (integer) $attributes['priority'] : 0;

                $listeners[$attributes['event']][$priority][] = array(array($id, $method), $class, $format);
            }
        }

        foreach ($container->findTaggedServiceIds('jms_serializer.event_subscriber') as $id => $tags) {
            $subscriberDefinition = $container->getDefinition($id);
            $subscriberClass = $container->getDefinition($id)->getClass();

            $subscriberClassReflectionObj = new \ReflectionClass($subscriberClass);

            if ( ! $subscriberClassReflectionObj->implementsInterface('JMS\SerializerBundle\Serializer\EventDispatcher\EventSubscriberInterface') ) {
                throw new \RuntimeException(sprintf('The service "%s" (class: %s) does not implement the EventSubscriberInterface.', $id, $subscriberClass));
            }

            if (!$subscriberDefinition->isPublic()) {
                throw new \RuntimeException(sprintf('The tag "jms_serializer.event_listener" of service "%s" requires the service to be public.', $id));
            }

            foreach (call_user_func(array($subscriberClass, 'getSubscribedEvents')) as $eventData) {
                if ( ! isset($eventData['event'])) {
                    throw new \RuntimeException(sprintf('The service "%s" (class: %s) must return an event for each subscribed event.', $id, $subscriberClass));
                }

                $class = isset($eventData['class']) ? strtolower($eventData['class']) : null;
                $format = isset($eventData['format']) ? $eventData['format'] : null;
                $method = isset($eventData['method']) ? $eventData['method'] : EventDispatcher::getDefaultMethodName($eventData['event']);
                $priority = isset($attributes['priority']) ? (integer) $attributes['priority'] : 0;

                $listeners[$eventData['event']][$priority][] = array(array($id, $method), $class, $format);
            }
        }

        if ($listeners) {
            array_walk($listeners, function (&$value, $key) {
                ksort($value);
            });

            foreach ($listeners as &$events) {
                $events = call_user_func_array('array_merge', $events);
            }

            $container->getDefinition('jms_serializer.event_dispatcher')
                ->addMethodCall('setListeners', array($listeners));
        }
    }
}
