<?php

namespace JMS\Serializer\Tests\Handler;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Handler\DateHandler;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\VisitorInterface;
use Metadata\MetadataFactoryInterface;

class DateHandlerTest extends \PHPUnit_Framework_TestCase
{

    private $date = '2017-06-18';

    public function testSerializeDate()
    {
        $handler = new DateHandler();
        $timezone = new \DateTimeZone('UTC');
        $datetime = \DateTime::createFromFormat('Y-m-d|', $this->date, $timezone);

        $visitor = $this->getMockBuilder(VisitorInterface::class)->getMock();
        $visitor->method('visitString')->with($this->date)->willReturn($this->date);
        $context = $this->getMockBuilder(SerializationContext::class)->getMock();

        // Test with type only
        $type = ['name' => 'DateTime', 'params' => ['Y-m-d']];
        $this->assertEquals(
            $this->date,
            $handler->serializeDateTime($visitor, $datetime, $type, $context)
        );

        // Test with deserialize type and empty timezone
        $type = ['name' => 'DateTime', 'params' => ['Y-m-d', '', 'Y-m-d|']];
        $this->assertEquals(
            $this->date,
            $handler->serializeDateTime($visitor, $datetime, $type, $context)
        );

        // Test with other deserialize type and empty timezone
        $type = ['name' => 'DateTime', 'params' => ['Y-m-d', '', 'Y']];
        $this->assertEquals(
            $this->date,
            $handler->serializeDateTime($visitor, $datetime, $type, $context)
        );
    }

    public function testDeserializeDate()
    {
        $handler = new DateHandler();
        $timezone = new \DateTimeZone('UTC');
        $datetime = \DateTime::createFromFormat('Y-m-d|', $this->date, $timezone);

        $visitor = $this->getMockBuilder(JsonDeserializationVisitor::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Test with type only
        // Might fail if the time is exactly 00:00:00.0000
        $type = ['name' => 'DateTime', 'params' => ['Y-m-d']];
        $this->assertNotEquals(
            $datetime,
            $handler->deserializeDateTimeFromJson($visitor, $this->date, $type)
        );

        // Test with deserialize type and empty timezone
        $type = ['name' => 'DateTime', 'params' => ['Y-m-d', '', 'Y-m-d|']];
        $this->assertEquals(
            $datetime,
            $handler->deserializeDateTimeFromJson($visitor, $this->date, $type)
        );
    }
}
