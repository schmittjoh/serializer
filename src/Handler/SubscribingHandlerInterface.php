<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

use JMS\Serializer\GraphNavigatorInterface;

interface SubscribingHandlerInterface
{
    /**
     * @return iterable<array{
     *     type: string,
     *     format: string,
     *     direction?: GraphNavigatorInterface::DIRECTION_*,
     *     method?: string,
     * }>
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     */
    public static function getSubscribingMethods();
}
