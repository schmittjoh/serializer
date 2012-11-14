<?php

namespace JMS\SerializerBundle\DependencyInjection\Compiler;

use JMS\SerializerBundle\Serializer\Handler\HandlerRegistry;
use JMS\SerializerBundle\Serializer\GraphNavigator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class CustomHandlersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $handlers = array();
        foreach ($container->findTaggedServiceIds('jms_serializer.handler') as $id => $tags) {
            foreach ($tags as $attrs) {
                if ( ! isset($attrs['type'], $attrs['format'])) {
                    throw new \RuntimeException(sprintf('Each tag named "jms_serializer.custom_handler" of service "%s" must have at least two attributes: "type", and "format".', $id));
                }

                $directions = array(GraphNavigator::DIRECTION_DESERIALIZATION, GraphNavigator::DIRECTION_SERIALIZATION);
                if (isset($attrs['direction'])) {
                    if ( ! defined($directionConstant = 'JMS\SerializerBundle\Serializer\GraphNavigator::DIRECTION_'.strtoupper($attrs['direction']))) {
                        throw new \RuntimeException(sprintf('The direction "%s" of tag "jms_serializer.custom_handler" of service "%s" does not exist.', $attrs['direction'], $id));
                    }

                    $directions = array(constant($directionConstant));
                }

                foreach ($directions as $direction) {
                    $method = isset($attrs['method']) ? $attrs['method'] : HandlerRegistry::getDefaultMethod($direction, $attrs['type'], $attrs['format']);
                    $handlers[$direction][$attrs['type']][$attrs['format']] = array($id, $method);
                }
            }
        }

        foreach ($container->findTaggedServiceIds('jms_serializer.subscribing_handler') as $id => $tags) {
            $class = $container->getDefinition($id)->getClass();
            $ref = new \ReflectionClass($class);
            if ( ! $ref->implementsInterface('JMS\SerializerBundle\Serializer\Handler\SubscribingHandlerInterface')) {
                throw new \RuntimeException(sprintf('The service "%s" must implement the SubscribingHandlerInterface.', $id));
            }

            foreach (call_user_func(array($class, 'getSubscribingMethods')) as $methodData) {
                if ( ! isset($methodData['format'], $methodData['type'])) {
                    throw new \RuntimeException(sprintf('Each method returned from getSubscribingMethods of service "%s" must have a "type", and "format" attribute.', $id));
                }

                $directions = array(GraphNavigator::DIRECTION_DESERIALIZATION, GraphNavigator::DIRECTION_SERIALIZATION);
                if (isset($methodData['direction'])) {
                    $directions = array($methodData['direction']);
                }

                foreach ($directions as $direction) {
                    $method = isset($methodData['method']) ? $methodData['method'] : HandlerRegistry::getDefaultMethod($direction, $methodData['type'], $methodData['format']);
                    $handlers[$direction][$methodData['type']][$methodData['format']] = array($id, $method);
                }
            }
        }

        $container->getDefinition('jms_serializer.handler_registry')
            ->addArgument($handlers);
    }
}
