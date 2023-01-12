<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType\Collection;

final class ConstructorPropertyPromotionWithoutDocblock
{
    public function __construct(
        private array $data,
    ) {
    }
}
