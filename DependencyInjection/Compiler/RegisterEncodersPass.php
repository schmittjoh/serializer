<?php

namespace JMS\SerializerBundle\DependencyInjection\Compiler;

use JMS\SerializerBundle\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class RegisterEncodersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $encoders = array();
        foreach ($container->findTaggedServiceIds('jms_serializer.encoder') as $id => $attributes) {
            if (!isset($attributes[0]['format'])) {
                throw new RuntimeException(sprintf('"format" attribute must be specified for service "%s" and tag "jms_serializer.encoder".', $id));
            }

            $encoders[$attributes[0]['format']] = $id;
        }

        foreach (array_keys($container->findTaggedServiceIds('jms_serializer.serializer')) as $id) {
            $container
                ->getDefinition($id)
                ->addArgument($encoders)
            ;
        }
    }
}