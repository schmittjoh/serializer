<?php

namespace JMS\SerializerBundle\Tests\Serializer;

use JMS\SerializerBundle\Serializer\Handler\HandlerRegistry;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\SerializerBundle\Metadata\Driver\AnnotationDriver;
use JMS\SerializerBundle\Serializer\LazyLoadingSerializer;
use JMS\SerializerBundle\Serializer\Construction\UnserializeObjectConstructor;
use Metadata\MetadataFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use JMS\SerializerBundle\DependencyInjection\JMSSerializerExtension;

class LazyLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected $serializer;

    protected function setUp()
    {
        $this->serializer = new LazyLoadingSerializer(
            new MetadataFactory(new AnnotationDriver(new AnnotationReader())),
            new HandlerRegistry(),
            new UnserializeObjectConstructor(),
            null,
            null,
            array('json' => 'jms_serializer.json_serialization_visitor')
        );

        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', true);
        $container->setParameter('kernel.cache_dir', sys_get_temp_dir().'/serializer');
        $container->setParameter('kernel.bundles', array());
        $extension = new JMSSerializerExtension();
        $extension->load(array(array()), $container);

        $this->serializer->setContainer($container);
    }

    public function testSetSerializeNull()
    {
        $this->serializer->setSerializeNull(true);
        $this->assertEquals('{"foo":"bar","baz":null}', $this->serializer->serialize(array('foo' => 'bar', 'baz' => null), 'json'));

    }
}
