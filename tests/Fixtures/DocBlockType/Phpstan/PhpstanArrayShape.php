<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType\Phpstan;

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
