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

use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\Alias;
use JMS\SerializerBundle\Exception\RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class JMSSerializerExtension extends ConfigurableExtension
{
    private $factories = array();

    public function addHandlerFactory(HandlerFactoryInterface $factory)
    {
        $this->factories[$factory->getConfigKey()] = $factory;
    }

    public function loadInternal(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(array(
                        __DIR__.'/../Resources/config/')));
        $loader->load('services.xml');

        // add factories as resource
        foreach ($this->factories as $factory) {
            $container->addObjectResource($factory);
        }

        // property naming
        $container
            ->getDefinition('jms_serializer.camel_case_naming_strategy')
            ->addArgument($config['property_naming']['separator'])
            ->addArgument($config['property_naming']['lower_case'])
        ;
        if ($config['property_naming']['enable_cache']) {
            $container
                ->getDefinition('jms_serializer.cache_naming_strategy')
                ->addArgument(new Reference((string) $container->getAlias('jms_serializer.naming_strategy')))
            ;
            $container->setAlias('jms_serializer.naming_strategy', 'jms_serializer.cache_naming_strategy');
        }

        // gather handlers
        $serializationHandlers = $deserializationHandlers = array();
        foreach ($config['handlers'] as $k => $handlerConfig) {
            $id = $this->factories[$k]->getHandlerId($container, $handlerConfig);
            $type = $this->factories[$k]->getType($handlerConfig);

            if (0 !== ($type & HandlerFactoryInterface::TYPE_SERIALIZATION)) {
                $serializationHandlers[] = new Reference($id);
            }

            if (0 !== ($type & HandlerFactoryInterface::TYPE_DESERIALIZATION)) {
                $deserializationHandlers[] = new Reference($id);
            }
        }

        foreach (array('json', 'xml', 'yaml') as $format) {
            $container
                ->getDefinition('jms_serializer.'.$format.'_serialization_visitor')
                ->replaceArgument(1, $serializationHandlers)
            ;
        }
        foreach (array('json', 'xml') as $format) {
            $container
                ->getDefinition('jms_serializer.'.$format.'_deserialization_visitor')
                ->replaceArgument(1, $deserializationHandlers)
            ;
        }

        // metadata
        if ('none' === $config['metadata']['cache']) {
            $container->removeAlias('jms_serializer.metadata.cache');
        } elseif ('file' === $config['metadata']['cache']) {
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
        $bundles = $container->getParameter('kernel.bundles');
        if ($config['metadata']['auto_detection']) {
            foreach ($bundles as $name => $class) {
                $ref = new \ReflectionClass($class);

                $directories[$ref->getNamespaceName()] = dirname($ref->getFileName()).'/Resources/config/serializer';
            }
        }
        foreach ($config['metadata']['directories'] as $directory) {
            $directory['path'] = rtrim(str_replace('\\', '/', $directory['path']), '/');

            if ('@' === $directory['path'][0]) {
                $bundleName = substr($directory['path'], 1, strpos($directory['path'], '/') - 1);

                if (!isset($bundles[$bundleName])) {
                    throw new RuntimeException(sprintf('The bundle "%s" has not been registered with AppKernel. Available bundles: %s', $bundleName, implode(', ', array_keys($bundles))));
                }

                $ref = new \ReflectionClass($bundles[$bundleName]);
                $directory['path'] = dirname($ref->getFileName()).substr($directory['path'], strlen('@'.$bundleName));
            }

            $directories[rtrim($directory['namespace_prefix'], '\\')] = rtrim($directory['path'], '\\/');
        }
        $container
            ->getDefinition('jms_serializer.metadata.file_locator')
            ->replaceArgument(0, $directories)
        ;

        $container
            ->setParameter('jms_serializer.json_serialization_visitor.options', $config['visitors']['json']['options'])
        ;

        $container
            ->setParameter('jms_serializer.xml_deserialization_visitor.document_whitelist', $config['visitors']['xml']['document_whitelist'])
        ;
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($container->getParameterBag()->resolveValue('%kernel.debug%'), $this->factories);
    }
}
