<?php

namespace JMS\SerializerBundle\Serializer;

interface SerializerAwareInterface
{
    function setSerializer(SerializerInterface $serializer);
}