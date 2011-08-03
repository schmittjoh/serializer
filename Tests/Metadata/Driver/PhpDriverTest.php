<?php

namespace JMS\SerializerBundle\Tests\Metadata\Driver;

use Metadata\Driver\FileLocator;
use JMS\SerializerBundle\Metadata\Driver\PhpDriver;

class PhpDriverTest extends BaseDriverTest
{
    protected function getDriver()
    {
        return new PhpDriver(new FileLocator(array(
            'JMS\SerializerBundle\Tests\Fixtures' => __DIR__.'/php',
        )));
    }
}