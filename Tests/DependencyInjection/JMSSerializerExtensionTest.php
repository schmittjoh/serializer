<?php

namespace JMS\SerializerBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\ResolveDefinitionTemplatesPass;

use JMS\SerializerBundle\JMSSerializerBundle;

use Annotations\Reader;

use JMS\SerializerBundle\Tests\Fixtures\VersionedObject;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use JMS\SerializerBundle\DependencyInjection\JMSSerializerExtension;

class JMSSerializerExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
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
        $extension = new JMSSerializerExtension();
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', true);
        $container->setParameter('kernel.cache_dir', sys_get_temp_dir());
        $container->set('annotation_reader', new Reader());
        $container->set('service_container', $container);
        $extension->load(array(array(
            'versions' => array('0.0.1', '1.1.1'),
        )), $container);

        $bundle = new JMSSerializerBundle();
        $bundle->build($container);

        $container->getCompilerPassConfig()->setOptimizationPasses(array(
            new ResolveDefinitionTemplatesPass(),
        ));
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        $factory = $container->get('serializer_factory');
        $object  = new VersionedObject('foo', 'bar');

        $serializer = $container->get('serializer');
        $this->assertEquals(json_encode(array('name' => 'bar')), $serializer->serialize($object, 'json'));

        $serializer = $factory->getSerializer('0.0.1');
        $this->assertEquals(json_encode(array('name' => 'foo')), $serializer->serialize($object, 'json'));

        $serializer = $factory->getSerializer('1.1.1');
        $this->assertEquals(json_encode(array('name' => 'bar')), $serializer->serialize($object, 'json'));
    }
}