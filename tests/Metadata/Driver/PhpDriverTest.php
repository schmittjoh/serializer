<?php

namespace JMS\Serializer\Tests\Metadata\Driver;

use JMS\Serializer\Metadata\Driver\PhpDriver;
use Metadata\Driver\FileLocator;

class PhpDriverTest extends BaseDriverTest
{
    protected function getDriver()
    {
        return new PhpDriver(new FileLocator(array(
            'JMS\Serializer\Tests\Fixtures' => __DIR__ . '/php',
        )));
    }
}
