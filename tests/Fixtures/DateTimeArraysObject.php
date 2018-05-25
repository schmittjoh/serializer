<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;


class DateTimeArraysObject
{
    /**
     * @var \DateTime[]
     * @Type("array<DateTime>")
     */
    private $arrayWithDefaultDateTime;

    /**
     * @var \DateTime[]
     * @Type("array<DateTime<'d.m.Y H:i:s'>>")
     */
    private $arrayWithFormattedDateTime;


    function __construct($arrayWithDefaultDateTime, $arrayWithFormattedDateTime)
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
