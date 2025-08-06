<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Discriminator;

use JMS\Serializer\Annotation as Serializer;

#[Serializer\Discriminator(
    field: 'type',
    virtual: false,
    map: [
        CartItem::TYPE_CLOTH => CartItemCloth::class,
        CartItem::TYPE_PHONE => CartItemPhone::class,
    ],
    default: CartItem::class,
)]
class CartItem
{
    public const TYPE_DEFAULT = 'default';
    public const TYPE_EAT = 'eat';
    public const TYPE_CLOTH = 'cloth';
    public const TYPE_PHONE = 'phone';

    #[Serializer\Type(name: 'string')]
    public $type;

    #[Serializer\Type(name: 'string')]
    public $name;

    #[Serializer\Type(name: 'float')]
    public $price;

    public function __construct($type, $name, $price)
    {
        $this->type = $type;
        $this->name = $name;
        $this->price = $price;
    }
}
