<?php

namespace JMS\SerializerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

interface HandlerFactoryInterface
{
    const TYPE_SERIALIZATION   = 1;
    const TYPE_DESERIALIZATION = 2;
    const TYPE_ALL             = 3;

    function getConfigKey();
    function addConfiguration(ArrayNodeDefinition $builder);
    function getType(array $config);
    function getHandlerId(ContainerBuilder $container, array $config);
}