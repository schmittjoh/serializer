<?php

namespace JMS\SerializerBundle\Tests\Metadata\Driver;

use Metadata\Driver\FileLocator;
use JMS\SerializerBundle\Metadata\Driver\YamlDriver;

class YamlDriverTest extends BaseDriverTest
{
    protected function getDriver()
    {
        return new YamlDriver(new FileLocator(array(
            'JMS\SerializerBundle\Tests\Fixtures' => __DIR__.'/yml',
        )));
    }
}