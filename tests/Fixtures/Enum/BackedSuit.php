<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\Enum;

enum BackedSuit: string
{
    case Hearts = 'H';
    case Diamonds = 'D';
    case Clubs = 'C';
    case Spades = 'S';
}
