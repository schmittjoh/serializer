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
        self::assertEquals('P0DT0S', $ATOMDateIntervalString);

        $ATOMDateIntervalString = $dtf->format(new \DateInterval('P0DT0S'));
        self::assertEquals('P0DT0S', $ATOMDateIntervalString);

        $ATOMDateIntervalString = $dtf->format(new \DateInterval('PT45M'));

        self::assertEquals('PT45M', $ATOMDateIntervalString);

        $ATOMDateIntervalString = $dtf->format(new \DateInterval('P2YT45M'));

        self::assertEquals('P2YT45M', $ATOMDateIntervalString);

        $ATOMDateIntervalString = $dtf->format(new \DateInterval('P2Y4DT6H8M16S'));

        self::assertEquals('P2Y4DT6H8M16S', $ATOMDateIntervalString);
    }

    public function testFormatKeepsFractionalSeconds()
    {
        $dtf = new DateHandler();

        $interval = new \DateInterval('PT10S');
        $interval->f = 0.5;
        self::assertEquals('PT10.5S', $dtf->format($interval));

        $interval = new \DateInterval('PT0S');
        $interval->f = 0.25;
        self::assertEquals('PT0.25S', $dtf->format($interval));

        $start = new \DateTimeImmutable('2021-01-01 00:00:00.000000');
        $end = new \DateTimeImmutable('2021-01-01 00:00:10.500000');
        self::assertEquals('PT10.5S', $dtf->format($start->diff($end)));
    }
}
