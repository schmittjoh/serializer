<?php

namespace JMS\Serializer\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;

class AnnotationDriverTest extends BaseDriverTest
{
    protected function getDriver()
    {
        return new AnnotationDriver(new AnnotationReader());
    }
}
