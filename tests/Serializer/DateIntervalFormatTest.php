<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Serializer;

use JMS\Serializer\Handler\DateHandler;
use PHPUnit\Framework\TestCase;

class DateIntervalFormatTest extends TestCase
{
    public function testFormat()
    {
        $dtf = new DateHandler();

        $ATOMDateIntervalString = $dtf->format(new \DateInterval('P0D'));
        self::assertEquals($ATOMDateIntervalString, 'P0DT0S');

        $ATOMDateIntervalString = $dtf->format(new \DateInterval('P0DT0S'));
        self::assertEquals($ATOMDateIntervalString, 'P0DT0S');

        $ATOMDateIntervalString = $dtf->format(new \DateInterval('PT45M'));

        self::assertEquals($ATOMDateIntervalString, 'PT45M');

        $ATOMDateIntervalString = $dtf->format(new \DateInterval('P2YT45M'));

        self::assertEquals($ATOMDateIntervalString, 'P2YT45M');

        $ATOMDateIntervalString = $dtf->format(new \DateInterval('P2Y4DT6H8M16S'));

        self::assertEquals($ATOMDateIntervalString, 'P2Y4DT6H8M16S');
    }
}
