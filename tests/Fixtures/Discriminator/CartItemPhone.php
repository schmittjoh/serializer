<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Discriminator;

use JMS\Serializer\Annotation as Serializer;

class CartItemPhone extends CartItem
{
    #[Serializer\Type(name: 'string')]
    public $os;

    public function __construct($type, $name, $price, $os)
    {
        parent::__construct($type, $name, $price);
        $this->os = $os;
    }
}
