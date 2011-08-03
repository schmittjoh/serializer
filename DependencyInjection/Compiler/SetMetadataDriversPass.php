<?php

namespace JMS\SerializerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class SetMetadataDriversPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $drivers = array();
        foreach ($container->findTaggedServiceIds('jms_serializer.metadata_driver') as $id => $attr) {
            $drivers[] = new Reference($id);
        }

        $container
            ->getDefinition('jms_serializer.metadata.chain_driver')
            ->addArgument($drivers)
        ;
    }
}