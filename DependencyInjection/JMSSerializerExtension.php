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

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\DependencyInjection\Alias;
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
        $config = $this->mergeConfigs($configs, $container->getParameter('kernel.debug'));
        $loader = new XmlFileLoader($container, new FileLocator(array(__DIR__.'/../Resources/config/')));
        $loader->load('services.xml');

        // property naming
        $container
            ->getDefinition('jms_serializer.camel_case_naming_strategy')
            ->addArgument($config['property_naming']['separator'])
            ->addArgument($config['property_naming']['lower_case'])
        ;

        // datetime handler
        if (isset($config['handlers']['datetime'])) {
            $container
                ->getDefinition('jms_serializer.datetime_handler')
                ->addArgument($config['handlers']['datetime']['format'])
                ->addArgument($config['handlers']['datetime']['default_timezone'])
            ;
        } else {
            $container->removeDefinition('jms_serializer.datetime_handler');
        }

        // array collection handler
        if (!$config['handlers']['array_collection']) {
            $container->removeDefinition('jms_serializer.array_collection_handler');
        }

        // metadata
        if ('none' === $config['metadata']['cache']) {
            $container->removeAlias('jms_serializer.metadata.cache');
        } else if ('file' === $config['metadata']['cache']) {
            $container
                ->getDefinition('jms_serializer.metadata.cache.file_cache')
                ->replaceArgument(0, $config['metadata']['file_cache']['dir'])
            ;

            $dir = $container->getParameterBag()->resolveValue($config['metadata']['file_cache']['dir']);
            if (!file_exists($dir)) {
                if (!$rs = @mkdir($dir, 0777, true)) {
                    throw new RuntimeException(sprintf('Could not create cache directory "%s".', $dir));
                }
            }
        } else {
            $container->setAlias('jms_serializer.metadata.cache', new Alias($config['metadata']['cache'], false));
        }
        $container
            ->getDefinition('jms_serializer.metadata_factory')
            ->replaceArgument(2, $config['metadata']['debug'])
        ;

        // directories
        $directories = array();
        if ($config['metadata']['auto_detection']) {
            foreach ($container->getParameter('kernel.bundles') as $name => $class) {
                $ref = new \ReflectionClass($class);

                $directories[$ref->getNamespaceName()] = dirname($ref->getFileName()).'/Resources/config/serializer';
            }
        }
        foreach ($config['metadata']['directories'] as $directory) {
            $directories[rtrim($directory['namespace_prefix'], '\\')] = rtrim($directory['path'], '\\/');
        }
        $container
            ->getDefinition('jms_serializer.metadata.file_locator')
            ->replaceArgument(0, $directories)
        ;

        // annotation driver
        if (!$config['metadata']['enable_annotations']) {
            $container->removeDefinition('jms_serializer.metadata.annotation_driver');
        }
    }

    private function mergeConfigs(array $configs, $debug)
    {
        $processor = new Processor();
        $config = new Configuration($debug);

        return $processor->process($config->getConfigTreeBuilder()->buildTree(), $configs);
    }
}