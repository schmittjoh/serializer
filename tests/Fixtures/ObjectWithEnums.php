<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Tests\Fixtures\Enum\BackedSuit;
use JMS\Serializer\Tests\Fixtures\Enum\BackedSuitInt;
use JMS\Serializer\Tests\Fixtures\Enum\Suit;

class ObjectWithEnums
{
    /**
     * @Serializer\Type("enum<'JMS\Serializer\Tests\Fixtures\Enum\Suit', 'name'>")
     */
    public Suit $ordinary;

    /**
     * @Serializer\Type("enum<'JMS\Serializer\Tests\Fixtures\Enum\BackedSuit', 'value'>")
     */
    public BackedSuit $backedValue;

    /**
     * @Serializer\Type("enum<'JMS\Serializer\Tests\Fixtures\Enum\BackedSuit'>")
     */
    public BackedSuit $backedWithoutParam;

    /**
     * @Serializer\Type("array<enum<'JMS\Serializer\Tests\Fixtures\Enum\Suit'>>")
     */
    public array $ordinaryArray;

    /**
     * @Serializer\Type("array<enum<'JMS\Serializer\Tests\Fixtures\Enum\BackedSuit', 'value'>>")
     */
    public array $backedArray;

    /**
     * @Serializer\Type("array<enum<'JMS\Serializer\Tests\Fixtures\Enum\BackedSuit'>>")
     */
    public array $backedArrayWithoutParam;

    public Suit $ordinaryAutoDetect;

    public BackedSuit $backedAutoDetect;

    public BackedSuitInt $backedIntAutoDetect;

    public BackedSuitInt $backedInt;

    public BackedSuit $backedName;

    public BackedSuitInt $backedIntForcedStr;

    public function __construct()
    {
        $this->ordinary = Suit::Clubs;

        $this->backedValue = BackedSuit::Clubs;
        $this->backedWithoutParam = BackedSuit::Clubs;

        $this->backedArray = [BackedSuit::Clubs, BackedSuit::Hearts];
        $this->backedArrayWithoutParam = [BackedSuit::Clubs, BackedSuit::Hearts];
        $this->ordinaryArray = [Suit::Clubs, Suit::Spades];

        $this->ordinaryAutoDetect = Suit::Clubs;
        $this->backedAutoDetect = BackedSuit::Clubs;
        $this->backedIntAutoDetect = BackedSuitInt::Clubs;

        $this->backedName = BackedSuit::Clubs;
        $this->backedInt = BackedSuitInt::Clubs;
        $this->backedIntForcedStr = BackedSuitInt::Clubs;
    }
}
