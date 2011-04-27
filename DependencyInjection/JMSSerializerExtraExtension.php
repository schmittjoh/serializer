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

namespace JMS\SerializerExtraBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class JMSSerializerExtraExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $config = $processor->process($this->getConfigTree(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(array(__DIR__.'/../Resources/config/')));
        $loader->load('services.xml');

        // set encoders
        $encoders = array();
        if ($config['encoders']['xml']) {
            $encoders['xml'] = new Reference('jms_serializer_extra.xml_encoder');
        }
        if ($config['encoders']['json']) {
            $encoders['json'] = new Reference('jms_serializer_extra.json_encoder');
        }
        if (!$encoders) {
            throw new \RuntimeException('No encoders have been configured.');
        }
        $container
            ->getDefinition('jms_serializer_extra.serializer_factory')
            ->addArgument($encoders)
        ;

        // naming strategy
        $container
            ->getDefinition('jms_serializer_extra.camel_case_naming_strategy')
            ->addArgument($config['naming_strategy']['separator'])
            ->addArgument($config['naming_strategy']['lower_case'])
        ;
    }

    private function getConfigTree()
    {
        $tb = new TreeBuilder();

        $tb->root('jms_serializer_extra', 'array')
                ->fixXmlConfig('encoder')
                ->children()
                   ->arrayNode('naming_strategy')
                       ->addDefaultsIfNotSet()
                       ->children()
                           ->scalarNode('separator')->defaultValue('_')->end()
                           ->booleanNode('lower_case')->defaultTrue()->end()
                       ->end()
                   ->end()
                   ->arrayNode('encoders')
                       ->addDefaultsIfNotSet()
                       ->children()
                           ->booleanNode('xml')->defaultTrue()->end()
                           ->booleanNode('json')->defaultTrue()->end()
                       ->end()
                   ->end()
               ->end()
           ->end();

        return $tb->buildTree();
    }
}