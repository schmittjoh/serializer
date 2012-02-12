<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\SerializerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private $debug;
    private $factories;

    public function __construct($debug = false, array $factories = array())
    {
        $this->debug = $debug;
        $this->factories = $factories;
    }

    public function getConfigTreeBuilder()
    {
        $tb = new TreeBuilder();

        $root = $tb
            ->root('jms_serializer', 'array')
                ->children()
        ;

        $this->addSerializersSection($root);
        $this->addMetadataSection($root);

        return $tb;
    }

    private function addSerializersSection(NodeBuilder $builder)
    {
        $builder
            ->arrayNode('property_naming')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('id')->cannotBeEmpty()->end()
                    ->scalarNode('separator')->defaultValue('_')->end()
                    ->booleanNode('lower_case')->defaultTrue()->end()
                    ->booleanNode('enable_cache')->defaultTrue()->end()
                ->end()
            ->end()
        ;

        $handlerNode = $builder
            ->arrayNode('handlers')
                ->addDefaultsIfNotSet()
                ->disallowNewKeysInSubsequentConfigs()
                ->children()
        ;

        foreach ($this->factories as $factory) {
            $factory->addConfiguration(
                $handlerNode->arrayNode($factory->getConfigKey())->canBeUnset());
        }
    }

    private function addMetadataSection(NodeBuilder $builder)
    {
        $builder
            ->arrayNode('metadata')
                ->addDefaultsIfNotSet()
                ->fixXmlConfig('directory', 'directories')
                ->children()
                    ->scalarNode('cache')->defaultValue('file')->end()
                    ->booleanNode('debug')->defaultValue($this->debug)->end()
                    ->arrayNode('file_cache')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('dir')->defaultValue('%kernel.cache_dir%/jms_serializer')->end()
                        ->end()
                    ->end()
                    ->booleanNode('auto_detection')->defaultTrue()->end()
                    ->arrayNode('directories')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('path')->isRequired()->end()
                                ->scalarNode('namespace_prefix')->defaultValue('')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
