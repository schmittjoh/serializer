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

namespace JMS\SerializerBundle\Tests\DependencyInjection;

use Symfony\Component\HttpKernel\KernelInterface;

use Symfony\Component\DependencyInjection\Compiler\InlineServiceDefinitionsPass;

use Symfony\Component\DependencyInjection\Compiler\RepeatedPass;

use Symfony\Component\DependencyInjection\Compiler\AnalyzeServiceReferencesPass;

use Symfony\Component\DependencyInjection\Compiler\RemoveUnusedDefinitionsPass;

use JMS\SerializerBundle\Tests\Fixtures\SimpleObject;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\DependencyInjection\Compiler\ResolveDefinitionTemplatesPass;
use JMS\SerializerBundle\JMSSerializerBundle;
use Doctrine\Common\Annotations\Reader;
use JMS\SerializerBundle\Tests\Fixtures\VersionedObject;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use JMS\SerializerBundle\DependencyInjection\JMSSerializerExtension;

class JMSSerializerExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->clearTempDir();
    }

    protected function tearDown()
    {
        $this->clearTempDir();
    }

    private function clearTempDir()
    {
        // clear temporary directory
        $dir = sys_get_temp_dir().'/serializer';
        if (is_dir($dir)) {
            foreach (new \RecursiveDirectoryIterator($dir) as $file) {
                $filename = $file->getFileName();
                if ('.' === $filename || '..' === $filename) {
                    continue;
                }

                @unlink($file->getPathName());
            }

            @rmdir($dir);
        }
    }

    public function testLoad()
    {
        $container = $this->getContainerForConfig(array(array()));

        $simpleObject = new SimpleObject('foo', 'bar');
        $versionedObject  = new VersionedObject('foo', 'bar');
        $serializer = $container->get('serializer');

        // test that all components have been wired correctly
        $this->assertEquals(json_encode(array('name' => 'bar')), $serializer->serialize($versionedObject, 'json'));
        $this->assertEquals($simpleObject, $serializer->deserialize($serializer->serialize($simpleObject, 'json'), get_class($simpleObject), 'json'));
        $this->assertEquals($simpleObject, $serializer->deserialize($serializer->serialize($simpleObject, 'xml'), get_class($simpleObject), 'xml'));

        $serializer->setVersion('0.0.1');
        $this->assertEquals(json_encode(array('name' => 'foo')), $serializer->serialize($versionedObject, 'json'));

        $serializer->setVersion('1.1.1');
        $this->assertEquals(json_encode(array('name' => 'bar')), $serializer->serialize($versionedObject, 'json'));
    }

    private function getContainerForConfig(array $configs, KernelInterface $kernel = null)
    {
        if (null === $kernel) {
            $kernel = $this->getMock('Symfony\Component\HttpKernel\KernelInterface');
            $kernel
                ->expects($this->any())
                ->method('getBundles')
                ->will($this->returnValue(array()))
            ;
        }

        $bundle = new JMSSerializerBundle($kernel);
        $extension = $bundle->getContainerExtension();

        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', true);
        $container->setParameter('kernel.cache_dir', sys_get_temp_dir());
        $container->setParameter('kernel.bundles', array());
        $container->set('annotation_reader', new AnnotationReader());
        $container->set('service_container', $container);
        $container->set('translator', $this->getMock('Symfony\\Component\\Translation\\TranslatorInterface'));
        $extension->load($configs, $container);

        $bundle->build($container);

        $container->getCompilerPassConfig()->setOptimizationPasses(array(
            new ResolveDefinitionTemplatesPass(),
        ));
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        return $container;
    }
}