<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests;

use JMS\Serializer\SerializerBuilder;

class SerializerBuilderStrategy
{
    public static function create(...$args)
    {
        $args += [null, null, false];
        if ('1' === getenv('ENABLE_ATTRIBUTES')) {
            $args[2] = true;
        }

        return SerializerBuilder::create(...$args);
    }
}
