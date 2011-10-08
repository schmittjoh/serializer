<?php

namespace JMS\SerializerBundle\DependencyInjection\Factory;

use JMS\SerializerBundle\DependencyInjection\HandlerFactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DateTimeFactory implements HandlerFactoryInterface
{
    public function getConfigKey()
    {
        return 'datetime';
    }

    public function getType(array $config)
    {
        return self::TYPE_ALL;
    }

    public function addConfiguration(ArrayNodeDefinition $builder)
    {
        $builder
            ->addDefaultsIfNotSet()
            ->canBeUnset()
            ->children()
                ->scalarNode('format')->defaultValue(\DateTime::ISO8601)->end()
                ->scalarNode('default_timezone')->defaultValue(date_default_timezone_get())->end()
            ->end()
        ;
    }

    public function getHandlerId(ContainerBuilder $container, array $config)
    {
        $container
            ->getDefinition('jms_serializer.datetime_handler')
            ->addArgument($config['format'])
            ->addArgument($config['default_timezone'])
        ;

        return 'jms_serializer.datetime_handler';
    }
}