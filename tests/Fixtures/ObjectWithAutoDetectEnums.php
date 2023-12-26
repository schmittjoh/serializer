<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Tests\Fixtures\Enum\BackedSuit;
use JMS\Serializer\Tests\Fixtures\Enum\Suit;

class ObjectWithAutoDetectEnums
{
    public array $ordinaryArrayAutoDetect;

    public array $backedArrayAutoDetect;

    public array $mixedArrayAutoDetect;

    public function __construct()
    {
        $this->backedArrayAutoDetect = [BackedSuit::Clubs, BackedSuit::Hearts];
        $this->ordinaryArrayAutoDetect = [Suit::Clubs, Suit::Spades];
        $this->mixedArrayAutoDetect = [Suit::Clubs, BackedSuit::Hearts];
    }
}
