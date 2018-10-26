<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Handler;

use JMS\Serializer\Handler\DateHandler;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
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

    public function setUp()
    {
        $this->handler = new DateHandler();
        $this->timezone = new \DateTimeZone('UTC');
    }

    public function getParams()
    {
        return [
            [['Y-m-d']],
            [['Y-m-d', '', 'Y-m-d|']],
            [['Y-m-d', '', 'Y']],
        ];
    }

    /**
     * @param array $params
     *
     * @doesNotPerformAssertions
     * @dataProvider getParams
     */
    public function testSerializeDate(array $params)
    {
        $context = $this->getMockBuilder(SerializationContext::class)->getMock();

        $visitor = $this->getMockBuilder(SerializationVisitorInterface::class)->getMock();
        $visitor->method('visitString')->with('2017-06-18');

        $datetime = new \DateTime('2017-06-18 14:30:59', $this->timezone);
        $type = ['name' => 'DateTime', 'params' => $params];
        $this->handler->serializeDateTime($visitor, $datetime, $type, $context);
    }

    public function testTimePartGetsRemoved()
    {
        $visitor = new JsonDeserializationVisitor();

        $type = ['name' => 'DateTime', 'params' => ['Y-m-d', '', 'Y-m-d|']];
        self::assertEquals(
            \DateTime::createFromFormat('Y-m-d|', '2017-06-18', $this->timezone),
            $this->handler->deserializeDateTimeFromJson($visitor, '2017-06-18', $type)
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
            $this->handler->deserializeDateTimeFromJson($visitor, '2017-06-18', $type)
        );

        // custom deserialization format specified
        $type = ['name' => 'DateTime', 'params' => ['Y-m-d', '', 'Y-m-d']];
        self::assertEquals(
            $expectedDateTime,
            $this->handler->deserializeDateTimeFromJson($visitor, '2017-06-18', $type)
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
            $actualDateTime->format(\DateTime::RFC3339)
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
            $actualDateTime->format(\DateTime::RFC3339)
        );
    }
}
