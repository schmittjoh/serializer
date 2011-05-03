<?php

namespace JMS\SerializerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $tb = new TreeBuilder();

        $tb
            ->root('jms_serializer', 'array')
                ->children()
                    ->arrayNode('versions')->prototype('scalar')->end()->end()
                    ->arrayNode('normalization')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('date_format')->defaultValue(\DateTime::ISO8601)->end()
                            ->arrayNode('naming')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('separator')->defaultValue('_')->end()
                                    ->booleanNode('lower_case')->defaultTrue()->end()
                                ->end()
                            ->end()
                            ->booleanNode('doctrine_support')->defaultTrue()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $tb;
    }
}