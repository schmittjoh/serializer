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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private $debug;

    public function __construct($debug = false)
    {
        $this->debug = $debug;
    }

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
                            ->booleanNode('normalizable_support')->defaultTrue()->end()
                        ->end()
                    ->end()
                    ->arrayNode('metadata')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('cache')->defaultValue('file')->end()
                            ->booleanNode('debug')->defaultValue($this->debug)->end()
                            ->arrayNode('file_cache')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('dir')->defaultValue('%kernel.cache_dir%/serializer')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $tb;
    }
}