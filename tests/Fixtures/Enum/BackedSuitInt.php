<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Enum;

enum BackedSuitInt: int
{
    case Hearts = 1;
    case Diamonds = 2;
    case Clubs = 3;
    case Spades = 4;
}
