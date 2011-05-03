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
    public function testLoad()
    {
        $extension = new JMSSerializerExtension();
        $container = new ContainerBuilder();
        $container->set('annotation_reader', new Reader());
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