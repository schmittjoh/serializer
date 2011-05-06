<?php

namespace JMS\SerializerBundle\Serializer;

class UnserializeInstanceCreator implements InstanceCreatorInterface
{
    public function createInstance(\ReflectionClass $class)
    {
        $name = $class->getName();

        return unserialize(sprintf('O:%d:"%s":0:{}', strlen($name), $name));
    }
}