<?php

namespace JMS\SerializerBundle\Serializer\EventDispatcher;

abstract class Events
{
    const PRE_SERIALIZE = 'serializer.pre_serialize';
    const POST_SERIALIZE = 'serializer.post_serialize';
    const POST_DESERIALIZE = 'serializer.post_deserialize';

    final private function __construct() { }
}
