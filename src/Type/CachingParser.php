<?php

declare(strict_types=1);

namespace JMS\Serializer\Type;

/**
 * @internal
 */
final class CachingParser implements ParserInterface
{
    /**
     * @var ParserInterface
     */
    private $inner;

    /**
     * @var array<string, array>
     */
    private $cache = [];

    public function __construct(ParserInterface $inner)
    {
        $this->inner = $inner;
    }

    public function parse(string $type): array
    {
        return $this->cache[$type] ?? $this->cache[$type] = $this->inner->parse($type);
    }
}
