<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Discriminator;

use JMS\Serializer\Annotation as Serializer;

class CartItemCloth extends CartItem
{
    #[Serializer\Type(name: 'string')]
    public $size;

    public function __construct($type, $name, $price, $size)
    {
        parent::__construct($type, $name, $price);
        $this->size = $size;
    }
}
