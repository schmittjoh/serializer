<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;

class DateTimeArraysObject
{
    /**
     * @var \DateTime[]
     * @Type("array<DateTime>")
     */
    #[Type(name: 'array<DateTime>')]
    private $arrayWithDefaultDateTime;

    /**
     * @var \DateTime[]
     * @Type("array<DateTimeInterface<'d.m.Y H:i:s'>>")
     */
    #[Type(name: 'array<DateTimeInterface<"d.m.Y H:i:s">>')]
    private $arrayWithFormattedDateTime;

    public function __construct($arrayWithDefaultDateTime, $arrayWithFormattedDateTime)
    {
        $this->arrayWithDefaultDateTime = $arrayWithDefaultDateTime;
        $this->arrayWithFormattedDateTime = $arrayWithFormattedDateTime;
    }

    /**
     * @return \DateTime[]
     */
    public function getArrayWithDefaultDateTime()
    {
        return $this->arrayWithDefaultDateTime;
    }

    /**
     * @return \DateTime[]
     */
    public function getArrayWithFormattedDateTime()
    {
        return $this->arrayWithFormattedDateTime;
    }
}
