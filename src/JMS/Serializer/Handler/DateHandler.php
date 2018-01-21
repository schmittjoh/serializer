<?php

/*
 * Copyright 2016 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\Serializer\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\VisitorInterface;
use JMS\Serializer\XmlDeserializationVisitor;
use JMS\Serializer\XmlSerializationVisitor;

class DateHandler implements SubscribingHandlerInterface
{
    private $defaultFormat;
    private $defaultTimezone;
    private $xmlCData;

    public static function getSubscribingMethods()
    {
        $methods = array();
        $deserializationTypes = array('DateTime', 'DateTimeImmutable', 'DateInterval');
        $serialisationTypes = array('DateTime', 'DateTimeImmutable', 'DateInterval');

        foreach (array('json', 'xml', 'yml') as $format) {

            foreach ($deserializationTypes as $type) {
                $methods[] = [
                    'type' => $type,
                    'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                    'format' => $format,
                ];
            }

            foreach ($serialisationTypes as $type) {
                $methods[] = array(
                    'type' => $type,
                    'format' => $format,
                    'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                    'method' => 'serialize' . $type,
                );
            }
        }

        return $methods;
    }

    public function __construct($defaultFormat = \DateTime::ISO8601, $defaultTimezone = 'UTC', $xmlCData = true)
    {
        $this->defaultFormat = $defaultFormat;
        $this->defaultTimezone = new \DateTimeZone($defaultTimezone);
        $this->xmlCData = $xmlCData;
    }

    private function serializeDateTimeInterface(
        VisitorInterface $visitor,
        \DateTimeInterface $date,
        array $type,
        Context $context
    )
    {
        if ($visitor instanceof XmlSerializationVisitor && false === $this->xmlCData) {
            return $visitor->visitSimpleString($date->format($this->getFormat($type)), $type, $context);
        }

        $format = $this->getFormat($type);
        if ('U' === $format) {
            return $visitor->visitInteger($date->format($format), $type, $context);
        }

        return $visitor->visitString($date->format($this->getFormat($type)), $type, $context);
    }

    public function serializeDateTime(VisitorInterface $visitor, \DateTime $date, array $type, Context $context)
    {
        return $this->serializeDateTimeInterface($visitor, $date, $type, $context);
    }

    public function serializeDateTimeImmutable(
        VisitorInterface $visitor,
        \DateTimeImmutable $date,
        array $type,
        Context $context
    )
    {
        return $this->serializeDateTimeInterface($visitor, $date, $type, $context);
    }

    public function serializeDateInterval(VisitorInterface $visitor, \DateInterval $date, array $type, Context $context)
    {
        $iso8601DateIntervalString = $this->format($date);

        if ($visitor instanceof XmlSerializationVisitor && false === $this->xmlCData) {
            return $visitor->visitSimpleString($iso8601DateIntervalString, $type, $context);
        }

        return $visitor->visitString($iso8601DateIntervalString, $type, $context);
    }

    private function isDataXmlNull($data)
    {
        $attributes = $data->attributes('xsi', true);
        return isset($attributes['nil'][0]) && (string)$attributes['nil'][0] === 'true';
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

        return $this->parseDateInterval($data);
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
        $format = $this->getDeserializationFormat($type);

        if ($immutable) {
            $datetime = \DateTimeImmutable::createFromFormat($format, (string)$data, $timezone);
        } else {
            $datetime = \DateTime::createFromFormat($format, (string)$data, $timezone);
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
        } catch (\Exception $e) {
            throw new RuntimeException(sprintf('Invalid dateinterval "%s", expected ISO 8601 format', $data), null, $e);
        }

        return $dateInterval;
    }

    /**
     * @param array $type
     *  @return string
     */
    private function getDeserializationFormat(array $type)
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
     * @return string
     * @param array $type
     */
    private function getFormat(array $type)
    {
        return isset($type['params'][0]) ? $type['params'][0] : $this->defaultFormat;
    }

    /**
     * @param \DateInterval $dateInterval
     * @return string
     */
    public function format(\DateInterval $dateInterval)
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
