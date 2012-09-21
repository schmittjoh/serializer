<?php

namespace JMS\SerializerBundle\DependencyInjection\Factory;

use JMS\SerializerBundle\DependencyInjection\HandlerFactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class NullHandlerFactory implements HandlerFactoryInterface
{
    public function getConfigKey()
    {
        return 'null_handler';
    }

    public function getType(array $config)
    {
        return self::TYPE_SERIALIZATION;
    }

    public function addConfiguration(ArrayNodeDefinition $builder)
    {
        $builder->addDefaultsIfNotSet();
    }

    public function getHandlerId(\Symfony\Component\DependencyInjection\ContainerBuilder $container, array $config)
    {
        return 'jms_serializer.null_handler';
    }
}
