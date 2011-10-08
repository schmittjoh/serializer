<?php

namespace JMS\SerializerBundle\DependencyInjection\Factory;

use JMS\SerializerBundle\DependencyInjection\HandlerFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class ObjectBasedFactory implements HandlerFactoryInterface
{
    public function getConfigKey()
    {
        return 'object_based';
    }

    public function getType(array $config)
    {
        $type = 0;

        if ($config['serialization']) {
            $type |= self::TYPE_SERIALIZATION;
        }
        if ($config['deserialization']) {
            $type |= self::TYPE_DESERIALIZATION;
        }

        return $type;
    }

    public function addConfiguration(ArrayNodeDefinition $node)
    {
        $node
            ->treatTrueLike(array('serialization' => true, 'deserialization' => true))
            ->treatNullLike(array('serialization' => true, 'deserialization' => true))
            ->treatFalseLike(array('serialization' => false, 'deserialization' => false))
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('serialization')->defaultFalse()->end()
                ->booleanNode('deserialization')->defaultFalse()->end()
            ->end()
        ;
    }

    public function getHandlerId(ContainerBuilder $container, array $config)
    {
        return 'jms_serializer.object_based_custom_handler';
    }
}