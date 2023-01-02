<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\DocBlockType\Collection;

final class ConstructorPropertyPromotion
{
    /**
     * @param string[] $data
     */
    public function __construct(
        private array $data,
    ) {
    }
}
