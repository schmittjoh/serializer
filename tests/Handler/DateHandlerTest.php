<?php

namespace JMS\Serializer\Tests\Handler;

use JMS\Serializer\Handler\DateHandler;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\VisitorInterface;

class DateHandlerTest extends \PHPUnit_Framework_TestCase
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
     * @dataProvider getParams
     * @param array $params
     */
    public function testSerializeDate(array $params)
    {
        $context = $this->getMockBuilder(SerializationContext::class)->getMock();

        $visitor = $this->getMockBuilder(VisitorInterface::class)->getMock();
        $visitor->method('visitString')->with('2017-06-18');

        $datetime = new \DateTime('2017-06-18 14:30:59', $this->timezone);
        $type = ['name' => 'DateTime', 'params' => $params];
        $this->handler->serializeDateTime($visitor, $datetime, $type, $context);
    }

    public function testTimePartGetsRemoved()
    {
        $visitor = $this->getMockBuilder(JsonDeserializationVisitor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $type = ['name' => 'DateTime', 'params' => ['Y-m-d', '', 'Y-m-d|']];
        $this->assertEquals(
            \DateTime::createFromFormat('Y-m-d|', '2017-06-18', $this->timezone),
            $this->handler->deserializeDateTimeFromJson($visitor, '2017-06-18', $type)
        );
    }

    public function testTimePartGetsPreserved()
    {
        $visitor = $this->getMockBuilder(JsonDeserializationVisitor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $expectedDateTime = \DateTime::createFromFormat('Y-m-d', '2017-06-18', $this->timezone);
        // if the test is executed exactly at midnight, it might not detect a possible failure since the time component will be "00:00:00
        // I know, this is a bit paranoid
        if ($expectedDateTime->format("H:i:s") === "00:00:00") {
            sleep(1);
            $expectedDateTime = \DateTime::createFromFormat('Y-m-d', '2017-06-18', $this->timezone);
        }

        // no custom deserialization format specified
        $type = ['name' => 'DateTime', 'params' => ['Y-m-d']];
        $this->assertEquals(
            $expectedDateTime,
            $this->handler->deserializeDateTimeFromJson($visitor, '2017-06-18', $type)
        );

        // custom deserialization format specified
        $type = ['name' => 'DateTime', 'params' => ['Y-m-d', '', 'Y-m-d']];
        $this->assertEquals(
            $expectedDateTime,
            $this->handler->deserializeDateTimeFromJson($visitor, '2017-06-18', $type)
        );
    }

    public function testTimeZoneGetsPreservedWithUnixTimestamp()
    {
        $visitor = $this->getMockBuilder(JsonDeserializationVisitor::class)
            ->disableOriginalConstructor()
            ->getMock();


        $timestamp = time();
        $timezone = 'Europe/Brussels';
        $type = ['name' => 'DateTime', 'params' => ['U', $timezone]];

        $expectedDateTime = \DateTime::createFromFormat('U', $timestamp);
        $expectedDateTime->setTimezone(new \DateTimeZone($timezone));

        $actualDateTime = $this->handler->deserializeDateTimeFromJson($visitor, $timestamp, $type);

        $this->assertEquals(
            $expectedDateTime->format(\DateTime::RFC3339),
            $actualDateTime->format(\DateTime::RFC3339)
        );
    }

    public function testImmutableTimeZoneGetsPreservedWithUnixTimestamp()
    {
        $visitor = $this->getMockBuilder(JsonDeserializationVisitor::class)
            ->disableOriginalConstructor()
            ->getMock();


        $timestamp = time();
        $timezone = 'Europe/Brussels';
        $type = ['name' => 'DateTimeImmutable', 'params' => ['U', $timezone]];

        $expectedDateTime = \DateTime::createFromFormat('U', $timestamp);
        $expectedDateTime->setTimezone(new \DateTimeZone($timezone));

        $actualDateTime = $this->handler->deserializeDateTimeImmutableFromJson($visitor, $timestamp, $type);

        $this->assertEquals(
            $expectedDateTime->format(\DateTime::RFC3339),
            $actualDateTime->format(\DateTime::RFC3339)
        );
    }
}
