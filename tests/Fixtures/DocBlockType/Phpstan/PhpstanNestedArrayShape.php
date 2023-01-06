<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType\Phpstan;

/**
 * @phpstan-type Settings array{
 *  amount: array{type: string, value: string|null},
 * }
 */
final class PhpstanNestedArrayShape
{
    /**
     * @var Settings
     */
    public $data;
}
