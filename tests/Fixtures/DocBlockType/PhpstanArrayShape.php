<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType;

/**
 * @phpstan-type Settings array{
 *  method: string,
 *  amount: array{type: string, value: string|null}
 * }
 */
final class PhpstanArrayShape
{
    /**
     * @var Settings
     */
    public $data;
}