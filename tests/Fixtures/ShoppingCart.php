<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;

class ShoppingCart
{
    /**
     * @Type("array<JMS\Serializer\Tests\Fixtures\Discriminator\CartItem>")
     */
    #[Type(name: 'array<JMS\Serializer\Tests\Fixtures\Discriminator\CartItem>')]
    public $items;

    public function __construct($items)
    {
        $this->items = $items;
    }
}
