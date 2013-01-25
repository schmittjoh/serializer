<?php

namespace JMS\Serializer\EventDispatcher;

abstract class Events
{
    const PRE_SERIALIZE = 'serializer.pre_serialize';
    const POST_SERIALIZE = 'serializer.post_serialize';
    const POST_DESERIALIZE = 'serializer.post_deserialize';
    const CIRCULAR_SERIALIZATION = 'serializer.circular_serialization';

    final private function __construct() { }
}
