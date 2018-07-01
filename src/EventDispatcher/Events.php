<?php

declare(strict_types=1);

namespace JMS\Serializer\EventDispatcher;

abstract class Events
{
    public const PRE_SERIALIZE = 'serializer.pre_serialize';
    public const POST_SERIALIZE = 'serializer.post_serialize';
    public const PRE_DESERIALIZE = 'serializer.pre_deserialize';
    public const POST_DESERIALIZE = 'serializer.post_deserialize';

    final private function __construct()
    {
    }
}
