<?php
/**
 * DateTimeArraysObject.php
 * 
 * @author Jens Hassler <lukey@skytrek.de>
 * @since  12/2013
 */
 
namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlMap;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlKeyValuePairs;


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

    /**
     * @var \DateTime[]
     * @Type("array<string,DateTime<'d.m.Y H:i:s'>>")
     * @XmlKeyValuePairs
     */
    private $namedArrayWithFormattedDate;

    function __construct($arrayWithDefaultDateTime, $arrayWithFormattedDateTime, $namedArrayWithFormattedDate)
    {
        $this->arrayWithDefaultDateTime    = $arrayWithDefaultDateTime;
        $this->arrayWithFormattedDateTime  = $arrayWithFormattedDateTime;
        $this->namedArrayWithFormattedDate = $namedArrayWithFormattedDate;
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

    /**
     * @return \DateTime[]
     */
    public function getNamedArrayWithFormattedDate()
    {
        return $this->namedArrayWithFormattedDate;
    }
}