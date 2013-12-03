<?php

/*
 * Copyright 2013 Johannes M. Schmitt <schmittjoh@gmail.com>
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
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\XmlDeserializationVisitor;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\VisitorInterface;
use JMS\Serializer\GraphNavigator;

/**
 * The DateHandler works as an Extension to create strings from DateTime or DateInterval object or create
 * them from strings
 * DateTime is possible for all formats in both ways (deserialization and serialisation)
 * DateIntval is possible for all formats in serialisation and for XML only for deserialization
 *
 * Class DateHandler
 * @package JMS\Serializer\Handler
 */
class DateHandler implements SubscribingHandlerInterface
{
    private $defaultFormat;
    private $defaultTimezone;

    /**
     * this method decides which method needs to be called for a specific Date object creation
     *
     * @return array
     */
    public static function getSubscribingMethods()
    {
        $methods = array();
        $types = array('DateTime', 'DateInterval');

        foreach (array('json', 'xml', 'yml') as $format) {
            $methods[] = array(
                'type' => 'DateTime',
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => $format,
            );
            //the DateInerval is no able to be deserialzed from xml
            if($format == "xml"){
                $methods[] = array(
                    'type' => 'DateInterval',
                    'format' => $format,
                    'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                    'method' => 'deserializeDateIntervalXml',
                );
            }
            foreach ($types as $type) {
                $methods[] = array(
                    'type' => $type,
                    'format' => $format,
                    'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                    'method' => 'serialize'.$type,
                );
            }
        }

        return $methods;
    }


    /**
     * @param string $defaultFormat
     * @param string $defaultTimezone
     */
    public function __construct($defaultFormat = \DateTime::ISO8601, $defaultTimezone = 'UTC')
    {
        $this->defaultFormat = $defaultFormat;
        $this->defaultTimezone = new \DateTimeZone($defaultTimezone);
    }

    /**
     * method to serialize a DateTime object to a formatted string
     *
     * @param VisitorInterface $visitor
     * @param \DateTime $date
     * @param array $type
     * @param Context $context
     * @return mixed
     */
    public function serializeDateTime(VisitorInterface $visitor, \DateTime $date, array $type, Context $context)
    {
        return $visitor->visitString($date->format($this->getFormat($type)), $type, $context);
    }

    /**
     * method to serialize a DateInterval object to a formatted string
     * @param VisitorInterface $visitor
     * @param \DateInterval $date
     * @param array $type
     * @param Context $context
     * @return mixed
     */
    public function serializeDateInterval(VisitorInterface $visitor, \DateInterval $date, array $type, Context $context)
    {
        $iso8601DateIntervalString = $this->format($date);

        return $visitor->visitString($iso8601DateIntervalString, $type, $context);
    }

    /**
     * method will parse the node for a "DateTime-string" and will give it to the factory method for the DateInterval
     * @param \JMS\Serializer\VisitorInterface|\JMS\Serializer\XmlDeserializationVisitor|\JMS\Serializer\XmlDomDeserializationVisitor $visitor
     * @param $data
     * @param array $type
     * @return \DateInterval | null
     */
    public function deserializeDateIntervalXml(XmlDeserializationVisitor $visitor, $data, array $type)
    {
        if($data instanceof \DOMDocument)
        {
            $data = $data->documentElement;

        }
        if($visitor->checkNullNode($data)){
            return null;
        }
        return $this->parseDateInterval($data->nodeValue,$type);
    }

    /**
     * with the help of the newer visitor this mehtod will deserialize a Date string into an DataTime Object
     *
     * @param XmlDeserializationVisitor $visitor
     * @param $data
     * @param array $type
     * @return \DateTime|null
     */
    public function deserializeDateTimeFromXml(XmlDeserializationVisitor $visitor, $data, array $type)
    {
        if($data instanceof \DOMDocument)
        {
            $data = $data->documentElement;

        }
        if($visitor->checkNullNode($data)){
            return null;
        }
        return $this->parseDateTime($data->nodeValue,$type);
    }


    /**
     * This method will create the DateTime object from information getting by the JsonDeserializer
     *
     * @param JsonDeserializationVisitor $visitor
     * @param $data
     * @param array $type
     * @return \DateTime|null
     */
    public function deserializeDateTimeFromJson(JsonDeserializationVisitor $visitor, $data, array $type)
    {
        if (null === $data) {
            return null;
        }

        return $this->parseDateTime($data, $type);
    }

    /**
     * if not set inside of the params this method will create a DataTime object with the default timezone
     *
     * @param $data
     * @param array $type
     * @return \DateTime
     * @throws \JMS\Serializer\Exception\RuntimeException
     */
    private function parseDateTime($data, array $type)
    {
        $timezone = isset($type['params'][1]) ? new \DateTimeZone($type['params'][1]) : $this->defaultTimezone;
        $format = $this->getFormat($type);
        $datetime = \DateTime::createFromFormat($format, (string) $data, $timezone);
        if (false === $datetime) {
            throw new RuntimeException(sprintf('Invalid datetime "%s", expected format %s.', $data, $format));
        }
        return $datetime;
    }


    /**
     * same as the parseDateTime this method will parse the string and return a new DateInterval object
     *
     * @param $data
     * @param array $type
     * @return \DateInterval
     * @throws \JMS\Serializer\Exception\RuntimeException
     */
    private function parseDateInterval($data,array $type)
    {
        $dateInterval = new \DateInterval($data);
        if(false === $dateInterval){
            throw new RuntimeException(sprintf('Invalid datetintervall "%s"', $data));
        }
        return $dateInterval;
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
            $format .= $dateInterval->y.'Y';
        }

        if (0 < $dateInterval->m) {
            $format .= $dateInterval->m.'M';
        }

        if (0 < $dateInterval->d) {
            $format .= $dateInterval->d.'D';
        }

        if (0 < $dateInterval->h || 0 < $dateInterval->i || 0 < $dateInterval->s) {
            $format .= 'T';
        }

        if (0 < $dateInterval->h) {
            $format .= $dateInterval->h.'H';
        }

        if (0 < $dateInterval->i) {
            $format .= $dateInterval->i.'M';
        }

        if (0 < $dateInterval->s) {
            $format .= $dateInterval->s.'S';
        }

        return $format;
    }
}
