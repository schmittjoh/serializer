<?php

namespace JMS\SerializerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class RegisterNormalizersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $normalizers = array();
        foreach ($container->findTaggedServiceIds('jms_serializer.normalizer') as $id => $attributes) {
            $def = $container->findDefinition($id);
            $strict = ContainerInterface::SCOPE_PROTOTYPE !== $def->getScope();
            $normalizers[] = new Reference($id, ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $strict);
        }

        foreach (array_keys($container->findTaggedServiceIds('jms_serializer.serializer')) as $id) {
            $container
                ->getDefinition($id)
                ->addArgument($normalizers)
            ;
        }
    }
}