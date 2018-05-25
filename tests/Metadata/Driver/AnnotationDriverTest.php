<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;

class AnnotationDriverTest extends BaseDriverTest
{
    protected function getDriver()
    {
        return new AnnotationDriver(new AnnotationReader(), new IdenticalPropertyNamingStrategy());
    }
}
