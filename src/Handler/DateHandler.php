<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use JMS\Serializer\XmlDeserializationVisitor;
use JMS\Serializer\XmlSerializationVisitor;
use function sprintf;

final class DateHandler implements SubscribingHandlerInterface
{
    private $defaultFormat;
    private $defaultTimezone;
    private $xmlCData;

    public static function getSubscribingMethods()
    {
        $methods              = [];
        $deserializationTypes = ['DateTime', 'DateTimeImmutable', 'DateInterval'];
        $serialisationTypes   = ['DateTime', 'DateTimeImmutable', 'DateInterval'];

        foreach (['json', 'xml'] as $format) {
            foreach ($deserializationTypes as $type) {
                $methods[] = [
                    'type' => $type,
                    'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                    'format' => $format,
                ];
            }

            foreach ($serialisationTypes as $type) {
                $methods[] = [
                    'type' => $type,
                    'format' => $format,
                    'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                    'method' => 'serialize' . $type,
                ];
            }
        }

        return $methods;
    }

    public function __construct($defaultFormat = \DateTime::ATOM, $defaultTimezone = 'UTC', $xmlCData = true)
    {
        $this->defaultFormat   = $defaultFormat;
        $this->defaultTimezone = new \DateTimeZone($defaultTimezone);
        $this->xmlCData        = $xmlCData;
    }

    private function serializeDateTimeInterface(
        SerializationVisitorInterface $visitor,
        \DateTimeInterface $date,
        array $type,
        SerializationContext $context
    ) {
        if ($visitor instanceof XmlSerializationVisitor && false === $this->xmlCData) {
            return $visitor->visitSimpleString($date->format($this->getFormat($type)), $type);
        }

        $format = $this->getFormat($type);
        if ('U' === $format) {
            return $visitor->visitInteger((int) $date->format($format), $type);
        }

        return $visitor->visitString($date->format($this->getFormat($type)), $type);
    }

    public function serializeDateTime(SerializationVisitorInterface $visitor, \DateTime $date, array $type, SerializationContext $context)
    {
        return $this->serializeDateTimeInterface($visitor, $date, $type, $context);
    }

    public function serializeDateTimeImmutable(
        SerializationVisitorInterface $visitor,
        \DateTimeImmutable $date,
        array $type,
        SerializationContext $context
    ) {
        return $this->serializeDateTimeInterface($visitor, $date, $type, $context);
    }

    public function serializeDateInterval(SerializationVisitorInterface $visitor, \DateInterval $date, array $type, SerializationContext $context)
    {
        $iso8601DateIntervalString = $this->format($date);

        if ($visitor instanceof XmlSerializationVisitor && false === $this->xmlCData) {
            return $visitor->visitSimpleString($iso8601DateIntervalString, $type);
        }

        return $visitor->visitString($iso8601DateIntervalString, $type);
    }

    private function isDataXmlNull($data)
    {
        $attributes = $data->attributes('xsi', true);
        return isset($attributes['nil'][0]) && (string) $attributes['nil'][0] === 'true';
    }

    public function deserializeDateTimeFromXml(XmlDeserializationVisitor $visitor, $data, array $type)
    {
        if ($this->isDataXmlNull($data)) {
            return null;
        }

        return $this->parseDateTime($data, $type);
    }

    public function deserializeDateTimeImmutableFromXml(XmlDeserializationVisitor $visitor, $data, array $type)
    {
        if ($this->isDataXmlNull($data)) {
            return null;
        }

        return $this->parseDateTime($data, $type, true);
    }

    public function deserializeDateIntervalFromXml(XmlDeserializationVisitor $visitor, $data, array $type)
    {
        if ($this->isDataXmlNull($data)) {
            return null;
        }

        return $this->parseDateInterval((string) $data);
    }

    public function deserializeDateTimeFromJson(JsonDeserializationVisitor $visitor, $data, array $type)
    {
        if (null === $data) {
            return null;
        }

        return $this->parseDateTime($data, $type);
    }

    public function deserializeDateTimeImmutableFromJson(JsonDeserializationVisitor $visitor, $data, array $type)
    {
        if (null === $data) {
            return null;
        }

        return $this->parseDateTime($data, $type, true);
    }

    public function deserializeDateIntervalFromJson(JsonDeserializationVisitor $visitor, $data, array $type)
    {
        if (null === $data) {
            return null;
        }

        return $this->parseDateInterval($data);
    }

    private function parseDateTime($data, array $type, $immutable = false)
    {
        $timezone = !empty($type['params'][1]) ? new \DateTimeZone($type['params'][1]) : $this->defaultTimezone;
        $format   = $this->getDeserializationFormat($type);

        if ($immutable) {
            $datetime = \DateTimeImmutable::createFromFormat($format, (string) $data, $timezone);
        } else {
            $datetime = \DateTime::createFromFormat($format, (string) $data, $timezone);
        }

        if (false === $datetime) {
            throw new RuntimeException(sprintf('Invalid datetime "%s", expected format %s.', $data, $format));
        }

        if ($format === 'U') {
            $datetime = $datetime->setTimezone($timezone);
        }

        return $datetime;
    }

    private function parseDateInterval($data)
    {
        $dateInterval = null;
        try {
            $dateInterval = new \DateInterval($data);
        } catch (\Throwable $e) {
            throw new RuntimeException(sprintf('Invalid dateinterval "%s", expected ISO 8601 format', $data), null, $e);
        }

        return $dateInterval;
    }

    /**
     * @param array $type
     */
    private function getDeserializationFormat(array $type): string
    {
        if (isset($type['params'][2])) {
            return $type['params'][2];
        }
        if (isset($type['params'][0])) {
            return $type['params'][0];
        }
        return $this->defaultFormat;
    }

    /**
     * @param array $type
     */
    private function getFormat(array $type): string
    {
        return $type['params'][0] ?? $this->defaultFormat;
    }

    public function format(\DateInterval $dateInterval): string
    {
        $format = 'P';

        if (0 < $dateInterval->y) {
            $format .= $dateInterval->y . 'Y';
        }

        if (0 < $dateInterval->m) {
            $format .= $dateInterval->m . 'M';
        }

        if (0 < $dateInterval->d) {
            $format .= $dateInterval->d . 'D';
        }

        if (0 < $dateInterval->h || 0 < $dateInterval->i || 0 < $dateInterval->s) {
            $format .= 'T';
        }

        if (0 < $dateInterval->h) {
            $format .= $dateInterval->h . 'H';
        }

        if (0 < $dateInterval->i) {
            $format .= $dateInterval->i . 'M';
        }

        if (0 < $dateInterval->s) {
            $format .= $dateInterval->s . 'S';
        }

        if ($format === 'P') {
            $format = 'P0DT0S';
        }

        return $format;
    }
}
