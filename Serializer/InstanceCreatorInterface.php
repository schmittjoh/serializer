<?php

namespace JMS\SerializerBundle\Serializer;

interface InstanceCreatorInterface
{
    function createInstance(\ReflectionClass $class);
}