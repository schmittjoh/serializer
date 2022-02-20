<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use DateTimeInterface;
use JMS\Serializer\Annotation as JMS;

class DateTimeContainer
{
    /**
     * @var DateTimeInterface
     * @JMS\Type("DateTimeInterface<'Y-m-d'>")
     * @JMS\SerializedName("custom")
     */
    public $customDateTime;

    public function __construct(DateTimeInterface $custom)
    {
        $this->customDateTime = $custom;
    }
}
