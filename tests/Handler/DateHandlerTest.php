<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Handler;

use JMS\Serializer\Handler\DateHandler;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

class DateHandlerTest extends TestCase
{
    /**
     * @var DateHandler
     */
    private $handler;
    /**
     * @var \DateTimeZone
     */
    private $timezone;

    protected function setUp(): void
    {
        $this->handler = new DateHandler();
        $this->timezone = new \DateTimeZone('UTC');
    }

    public static function getParams()
    {
        return [
            [['Y-m-d']],
            [['Y-m-d', '', 'Y-m-d|']],
            [['Y-m-d', '', 'Y']],
            [['Y-m-d', '', ['Y-m-d', 'Y/m/d']]],
        ];
    }

    /**
     * @doesNotPerformAssertions
     * @dataProvider getParams
     */
    #[DataProvider('getParams')]
    #[DoesNotPerformAssertions]
    public function testSerializeDate(array $params)
    {
        $context = $this->getMockBuilder(SerializationContext::class)->getMock();

        $visitor = $this->getMockBuilder(SerializationVisitorInterface::class)->getMock();
        $visitor->method('visitString')->with('2017-06-18');

        $datetime = new \DateTime('2017-06-18 14:30:59', $this->timezone);
        $type = ['name' => 'DateTime', 'params' => $params];
        $this->handler->serializeDateTime($visitor, $datetime, $type, $context);
    }

    /**
     * @dataProvider getDeserializeDateInterval
     */
    #[DataProvider('getDeserializeDateInterval')]
    public function testDeserializeDateInterval(string $dateInterval, array $expected)
    {
        $visitor = $this->getMockBuilder(DeserializationVisitorInterface::class)->getMock();
        $visitor->method('visitString')->with('2017-06-18');

        $deserialized = $this->handler->deserializeDateIntervalFromJson($visitor, $dateInterval, []);
        if (isset($deserialized->f)) {
            self::assertEquals($expected['f'], $deserialized->f);
        }

        self::assertEquals($expected['s'], $deserialized->s);
    }

    public static function getDeserializeDateInterval()
    {
        return [
            ['P0Y0M0DT3H5M7.520S', ['s' => 7, 'f' => 0.52]],
            ['P0Y0M0DT3H5M7S', ['s' => 7, 'f' => 0]],
        ];
    }

    public function testTimePartGetsRemoved()
    {
        $visitor = new JsonDeserializationVisitor();

        $type = ['name' => 'DateTime', 'params' => ['Y-m-d', '', 'Y-m-d|']];
        self::assertEquals(
            \DateTime::createFromFormat('Y-m-d|', '2017-06-18', $this->timezone),
            $this->handler->deserializeDateTimeFromJson($visitor, '2017-06-18', $type),
        );
    }

    public function testMultiFormatCase()
    {
        $visitor = new JsonDeserializationVisitor();

        $type = ['name' => 'DateTime', 'params' => ['Y-m-d', '', ['Y-m-d|', 'Y/m/d']]];
        self::assertEquals(
            \DateTime::createFromFormat('Y/m/d', '2017/06/18', $this->timezone),
            $this->handler->deserializeDateTimeFromJson($visitor, '2017/06/18', $type),
        );
    }

    public function testTimePartGetsPreserved()
    {
        $visitor = new JsonDeserializationVisitor();

        $expectedDateTime = \DateTime::createFromFormat('Y-m-d', '2017-06-18', $this->timezone);
        // if the test is executed exactly at midnight, it might not detect a possible failure since the time component will be "00:00:00
        // I know, this is a bit paranoid
        if ('00:00:00' === $expectedDateTime->format('H:i:s')) {
            sleep(1);
            $expectedDateTime = \DateTime::createFromFormat('Y-m-d', '2017-06-18', $this->timezone);
        }

        // no custom deserialization format specified
        $type = ['name' => 'DateTime', 'params' => ['Y-m-d']];
        self::assertEquals(
            $expectedDateTime,
            $this->handler->deserializeDateTimeFromJson($visitor, '2017-06-18', $type),
        );

        // custom deserialization format specified
        $type = ['name' => 'DateTime', 'params' => ['Y-m-d', '', 'Y-m-d']];
        self::assertEquals(
            $expectedDateTime,
            $this->handler->deserializeDateTimeFromJson($visitor, '2017-06-18', $type),
        );
    }

    public function testTimeZoneGetsPreservedWithUnixTimestamp()
    {
        $visitor = new JsonDeserializationVisitor();

        $timestamp = (string) time();
        $timezone = 'Europe/Brussels';
        $type = ['name' => 'DateTime', 'params' => ['U', $timezone]];

        $expectedDateTime = \DateTime::createFromFormat('U', $timestamp);
        $expectedDateTime->setTimezone(new \DateTimeZone($timezone));

        $actualDateTime = $this->handler->deserializeDateTimeFromJson($visitor, $timestamp, $type);

        self::assertEquals(
            $expectedDateTime->format(\DateTime::RFC3339),
            $actualDateTime->format(\DateTime::RFC3339),
        );
    }

    public function testImmutableTimeZoneGetsPreservedWithUnixTimestamp()
    {
        $visitor = new JsonDeserializationVisitor();

        $timestamp = (string) time();
        $timezone = 'Europe/Brussels';
        $type = ['name' => 'DateTimeImmutable', 'params' => ['U', $timezone]];

        $expectedDateTime = \DateTime::createFromFormat('U', $timestamp);
        $expectedDateTime->setTimezone(new \DateTimeZone($timezone));

        $actualDateTime = $this->handler->deserializeDateTimeImmutableFromJson($visitor, $timestamp, $type);

        self::assertEquals(
            $expectedDateTime->format(\DateTime::RFC3339),
            $actualDateTime->format(\DateTime::RFC3339),
        );
    }

    public function testDefaultFormat()
    {
        $visitor = new JsonDeserializationVisitor();

        $type = ['name' => 'DateTime'];
        self::assertEquals(
            \DateTime::createFromFormat('Y/m/d H:i:s', '2017/06/18 17:32:11', $this->timezone),
            $this->handler->deserializeDateTimeFromJson($visitor, '2017-06-18T17:32:11Z', $type),
        );
    }
}
