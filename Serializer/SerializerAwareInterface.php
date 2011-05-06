<?php

namespace JMS\SerializerBundle\Serializer;

use Symfony\Component\Serializer\SerializerInterface;

interface SerializerAwareInterface
{
    function setSerializer(SerializerInterface $serializer);
}