<?php

declare(strict_types=1);

namespace JMS\Serializer\Type;

interface ParserInterface
{
    public function parse(string $type): array;
}
