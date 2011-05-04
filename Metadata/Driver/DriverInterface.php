<?php

namespace JMS\SerializerBundle\Metadata\Driver;

interface DriverInterface
{
    function loadMetadataForClass(\ReflectionClass $class);
}