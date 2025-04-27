<?php

declare(strict_types=1);

namespace JMS\Serializer\Type;

/**
 * @phpstan-import-type TypeArray from Type
 */
interface ParserInterface
{
    /**
     * @return TypeArray
     */
    public function parse(string $type): array;
}
