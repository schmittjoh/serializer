<?php

namespace JMS\SerializerBundle\Serializer;

interface SerializerInterface
{
    function serialize($data, $format);
    function deserialize($data, $type, $format);
}