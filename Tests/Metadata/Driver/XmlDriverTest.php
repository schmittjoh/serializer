<?php

namespace JMS\SerializerBundle\Tests\Metadata\Driver;

use Metadata\Driver\FileLocator;
use JMS\SerializerBundle\Metadata\Driver\XmlDriver;

class XmlDriverTest extends BaseDriverTest
{
    protected function getDriver()
    {
        return new XmlDriver(new FileLocator(array(
            'JMS\SerializerBundle\Tests\Fixtures' => __DIR__.'/xml',
        )));
    }
}