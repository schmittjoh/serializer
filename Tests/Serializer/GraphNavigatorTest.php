<?php

namespace JMS\SerializerBundle\Tests\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\SerializerBundle\Metadata\Driver\AnnotationDriver;
use JMS\SerializerBundle\Serializer\GraphNavigator;
use Metadata\MetadataFactory;

class GraphNavigatorTest extends \PHPUnit_Framework_TestCase
{
    private $metadataFactory;
    private $navigator;
    private $visitor;

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Resources are not supported in serialized data.
     */
    public function testResourceThrowsException()
    {
        $this->navigator->accept(STDIN, null, $this->visitor);
    }

    protected function setUp()
    {
        $this->visitor = $this->getMock('JMS\SerializerBundle\Serializer\VisitorInterface');

        $this->metadataFactory = new MetadataFactory(new AnnotationDriver(new AnnotationReader()));
        $this->navigator = new GraphNavigator(GraphNavigator::DIRECTION_SERIALIZATION, $this->metadataFactory);
    }
}
