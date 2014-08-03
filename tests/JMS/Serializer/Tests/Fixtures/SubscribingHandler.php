<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Handler\SubscribingHandlerInterface;

abstract class SubscribingHandler implements SubscribingHandlerInterface
{
    static protected $subscribingMethods = array();

    public static function getSubscribingMethods()
    {
        if (isset(static::$subscribingMethods[get_called_class()])) {
            return static::$subscribingMethods[get_called_class()];
        }

        return [];
    }

    public static function setSubscribingMethods(array $subscribingMethods)
    {
        static::$subscribingMethods[get_called_class()] = $subscribingMethods;
    }
}
