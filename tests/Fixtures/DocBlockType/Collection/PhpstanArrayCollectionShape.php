<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType\Collection;

/**
 * @phpstan-type Settings array<int, Product>
 */
final class PhpstanArrayCollectionShape
{
    /**
     * @var Settings
     */
    public $data;
}
