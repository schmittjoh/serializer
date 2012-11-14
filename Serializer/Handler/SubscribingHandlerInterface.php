<?php

namespace JMS\SerializerBundle\Serializer\Handler;

interface SubscribingHandlerInterface
{
    /**
     * @return array
     */
    public static function getSubscribingMethods();
}
