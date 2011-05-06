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

use Symfony\Component\DependencyInjection\DefinitionDecorator;
use JMS\SerializerBundle\Exception\RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class JMSSerializerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->mergeConfigs($configs);
        $loader = new XmlFileLoader($container, new FileLocator(array(__DIR__.'/../Resources/config/')));
        $loader->load('services.xml');

        // normalization
        $container
            ->getDefinition('jms_serializer.camel_case_naming_strategy')
            ->addArgument($config['normalization']['naming']['separator'])
            ->addArgument($config['normalization']['naming']['lower_case'])
        ;
        $container
            ->getDefinition('jms_serializer.native_php_type_normalizer')
            ->addArgument($config['normalization']['date_format'])
        ;
        if ($config['normalization']['doctrine_support']) {
            $container
                ->getDefinition('jms_serializer.array_collection_normalizer')
                ->addTag('jms_serializer.normalizer')
            ;
        }
        if ($config['normalization']['normalizable_support']) {
            $container
                ->getDefinition('jms_serializer.normalizable_object_normalizer')
                ->addTag('jms_serializer.normalizer')
            ;
        }

        // versions
        if ($config['versions']) {
            $serializers = array();
            foreach ($config['versions'] as $version) {
                $id = md5($version).sha1($version);

                $container
                    ->setDefinition(
                        $allDefId = 'jms_serializer.disjunct_exclusion_strategy.all.'.$id,
                        $allDef = new DefinitionDecorator('jms_serializer.disjunct_exclusion_strategy')
                    )
                ;
                $container
                    ->setDefinition(
                        $noneDefId = 'jms_serializer.disjunct_exclusion_strategy.none.'.$id,
                        $noneDef = new DefinitionDecorator('jms_serializer.disjunct_exclusion_strategy')
                    )
                ;

                $versionExDef = new DefinitionDecorator('jms_serializer.version_exclusion_strategy');
                $versionExDef->addArgument($version);
                $container->setDefinition($versionExDefId = 'jms_serializer.version_exclusion_strategy.'.$id, $versionExDef);

                $allDef->addArgument(array(
                    new Reference($versionExDefId),
                    new Reference('jms_serializer.all_exclusion_strategy'),
                ));
                $noneDef->addArgument(array(
                    new Reference($versionExDefId),
                    new Reference('jms_serializer.none_exclusion_strategy'),
                ));

                $strategies = array(
                    'ALL' => new Reference($allDefId),
                    'NONE' => new Reference($noneDefId),
                );

                $container->setDefinition(
                    $factoryDefId = 'jms_serializer.exclusion_strategy_factory.'.$id,
                    $factoryDef = new DefinitionDecorator('jms_serializer.exclusion_strategy_factory')
                );
                $factoryDef->addArgument($strategies);

                $container->setDefinition(
                    $propertyBasedDefId = 'jms_serializer.property_based_normalizer.'.$id,
                    $propertyBasedDef = new DefinitionDecorator('jms_serializer.property_based_normalizer')
                );
                $propertyBasedDef->addArgument(new Reference($factoryDefId));

                $container->setDefinition(
                    $serDefId = 'jms_serializer.serializer.'.$id,
                    $serDef = new DefinitionDecorator('jms_serializer.serializer')
                );
                $serDef->addTag('jms_serializer.serializer');
                $serDef->addArgument(new Reference($propertyBasedDefId));

                $serializers[$version] = $serDefId;
            }

            $container
                ->getDefinition('jms_serializer.serializer_factory')
                ->addArgument($serializers)
            ;
        }
    }

    private function mergeConfigs(array $configs)
    {
        $processor = new Processor();
        $config = new Configuration();

        return $processor->process($config->getConfigTreeBuilder()->buildTree(), $configs);
    }
}