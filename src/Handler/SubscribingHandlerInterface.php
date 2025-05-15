<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

interface SubscribingHandlerInterface
{
    /**
     * Return format:
     *
     *      array(
     *          array(
     *              'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
     *              'format' => 'json',
     *              'type' => 'DateTime',
     *              'method' => 'serializeDateTimeToJson',
     *          ),
     *      )
     *
     * The direction and method keys can be omitted.
     *
     * @return iterable<string, int|string>
     */
    public static function getSubscribingMethods(): iterable;
}
