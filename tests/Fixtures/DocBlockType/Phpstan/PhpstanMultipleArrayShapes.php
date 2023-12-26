<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType\Phpstan;

/**
 * @phpstan-type Settings array{
 *  method: string,
 *  amount: array{type: string, value: string|null}
 * }
 * @phpstan-type Details array{
 *  method: string
 * }
 */
final class PhpstanMultipleArrayShapes
{
    /**
     * @var Settings
     */
    public $data;

    /**
     * @var Details
     */
    public $details;
}
