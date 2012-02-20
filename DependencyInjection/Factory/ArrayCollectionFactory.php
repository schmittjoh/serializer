<?php

namespace JMS\SerializerBundle\DependencyInjection\Factory;

use JMS\SerializerBundle\DependencyInjection\HandlerFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class ArrayCollectionFactory implements HandlerFactoryInterface
{
    public function getConfigKey()
    {
        return 'array_collection';
    }

    public function getType(array $config)
    {
        return self::TYPE_DESERIALIZATION;
    }

    public function addConfiguration(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
        ;
    }

    public function getHandlerId(ContainerBuilder $container, array $config)
    {
        return 'jms_serializer.array_collection_handler';
    }
}
