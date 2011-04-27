<?php

namespace JMS\SerializerExtraBundle\Tests\DependencyInjection;

use JMS\SerializerExtraBundle\Tests\Fixtures\VersionedObject;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use JMS\SerializerExtraBundle\DependencyInjection\JMSSerializerExtraExtension;

class JMSSerializerExtraExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $extension = new JMSSerializerExtraExtension();
        $container = new ContainerBuilder();
        $extension->load(array(array()), $container);

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