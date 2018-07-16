<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlKeyValuePairs;

class NamedDateTimeImmutableArraysObject
{
    /**
     * @var \DateTime[]
     * @Type("array<string,DateTimeImmutable<'d.m.Y H:i:s'>>")
     * @XmlKeyValuePairs
     */
    private $namedArrayWithFormattedDate;

    public function __construct($namedArrayWithFormattedDate)
    {
        $this->namedArrayWithFormattedDate = $namedArrayWithFormattedDate;
    }

    /**
     * @return \DateTimeImmutable[]
     */
    public function getNamedArrayWithFormattedDate()
    {
        return $this->namedArrayWithFormattedDate;
    }
}
