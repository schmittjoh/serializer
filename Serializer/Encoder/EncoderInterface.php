<?php

namespace JMS\SerializerBundle\Serializer\Encoder;

interface EncoderInterface
{
    function encode($data);
    function decode($data);
}