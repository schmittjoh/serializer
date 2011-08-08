<?php

namespace JMS\SerializerBundle\Tests\Metadata\Driver;

use Metadata\Driver\FileLocator;
use JMS\SerializerBundle\Metadata\Driver\XmlDriver;

class XmlDriverTest extends BaseDriverTest
{
    /**
     * @expectedException JMS\SerializerBundle\Exception\XmlErrorException
     * @expectedExceptionMessage [FATAL] Start tag expected, '<' not found
     */
    public function testInvalidXml()
    {
        $driver = $this->getDriver();

        $ref = new \ReflectionMethod($driver, 'loadMetadataFromFile');
        $ref->setAccessible(true);
        $ref->invoke($driver, new \ReflectionClass('stdClass'), __DIR__.'/xml/invalid.xml');
    }

    protected function getDriver()
    {
        return new XmlDriver(new FileLocator(array(
            'JMS\SerializerBundle\Tests\Fixtures' => __DIR__.'/xml',
        )));
    }
}