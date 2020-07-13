<?php

declare(strict_types=1);

namespace JMS\Serializer\Exception;

/**
 * Throw this exception from you custom (de)serialization handler
 * in order to fallback to the default (de)serialization behavior.
 */
class SkipHandlerException extends RuntimeException
{
}
