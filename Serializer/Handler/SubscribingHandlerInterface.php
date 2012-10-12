<?php

namespace JMS\SerializerBundle\Serializer\Handler;

interface SubscribingHandlerInterface
{
    public static function getSubscribingMethods();
}