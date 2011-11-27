<?php

namespace JMS\SerializerBundle\DependencyInjection\Factory;

use JMS\SerializerBundle\DependencyInjection\HandlerFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class DoctrineOrmProxyFactory implements HandlerFactoryInterface
{
    public function getConfigKey()
    {
        return 'doctrine_orm_proxy';
    }

    public function getType(array $config)
    {
        return self::TYPE_SERIALIZATION;
    }

    public function addConfiguration(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
            ->defaultValue(array())
        ;
    }

    public function getHandlerId(ContainerBuilder $container, array $config)
    {
        return 'jms_serializer.doctrine_orm_handler';
    }
}