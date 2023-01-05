<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType\Phpstan;

/**
 * @phpstan-type Settings array<int, ProductType>
 */
final class PhpstanArrayCollectionShape
{
    /**
     * @var Settings
     */
    public $data;
}
