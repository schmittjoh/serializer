<?php

namespace JMS\Serializer\Handler;

interface SubscribingHandlerInterface
{
    /**
     * @return array
     */
    public static function getSubscribingMethods();
}
