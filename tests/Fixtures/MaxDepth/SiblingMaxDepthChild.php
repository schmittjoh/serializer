<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\MaxDepth;

class SiblingMaxDepthChild
{
    public string $name;

    public ?SiblingMaxDepthChild $child = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
