<?php

namespace JMS\SerializerBundle\Serializer\Encoder;

class JsonEncoder implements EncoderInterface
{
    public function encode($data)
    {
        return json_encode($data);
    }

    public function decode($data)
    {
        return json_decode($data, true);
    }
}