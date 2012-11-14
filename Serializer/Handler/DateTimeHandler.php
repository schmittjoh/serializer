<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\SerializerBundle\Serializer\Handler;

use JMS\SerializerBundle\Serializer\JsonDeserializationVisitor;
use Symfony\Component\Yaml\Inline;
use JMS\SerializerBundle\Serializer\YamlSerializationVisitor;
use JMS\SerializerBundle\Serializer\XmlDeserializationVisitor;
use JMS\SerializerBundle\Exception\RuntimeException;
use JMS\SerializerBundle\Serializer\JsonSerializationVisitor;
use JMS\SerializerBundle\Serializer\XmlSerializationVisitor;

class DateTimeHandler implements SubscribingHandlerInterface
{
    private $defaultFormat;
    private $defaultTimezone;

    public static function getSubscribingMethods()
    {
        $methods = array();
        foreach (array('json', 'xml', 'yml') as $format) {
            $methods[] = array(
                'type' => 'DateTime',
                'format' => $format,
            );
        }

        return $methods;
    }

    public function __construct($defaultFormat = \DateTime::ISO8601, $defaultTimezone = 'UTC')
    {
        $this->defaultFormat = $defaultFormat;
        $this->defaultTimezone = new \DateTimeZone($defaultTimezone);
    }

    public function serializeDateTimeToXml(XmlSerializationVisitor $visitor, \DateTime $date, array $type)
    {
        if (null === $visitor->document) {
            $visitor->document = $visitor->createDocument(null, null, true);
        }

        return $visitor->document->createTextNode($date->format($this->getFormat($type)));
    }

    public function serializeDateTimeToYml(YamlSerializationVisitor $visitor, \DateTime $date, array $type)
    {
        return Inline::dump($date->format($this->getFormat($type)));
    }

    public function serializeDateTimeToJson(JsonSerializationVisitor $visitor, \DateTime $date, array $type)
    {
        return $date->format($this->getFormat($type));
    }

    public function deserializeDateTimeFromXml(XmlDeserializationVisitor $visitor, $data, array $type)
    {
        return $this->parseDateTime($data, $type);
    }

    public function deserializeDateTimeFromJson(JsonDeserializationVisitor $visitor, $data, array $type)
    {
        return $this->parseDateTime($data, $type);
    }

    private function parseDateTime($data, array $type)
    {
        $timezone = isset($type['params'][1]) ? $type['params'][1] : $this->defaultTimezone;
        $datetime = \DateTime::createFromFormat($this->getFormat($type), (string) $data, $timezone);
        if (false === $datetime) {
            throw new RuntimeException(sprintf('Invalid datetime "%s", expected format %s.', $data, $this->defaultFormat));
        }

        return $datetime;
    }

    /**
     * @return string
     * @param array $type
     */
    private function getFormat(array $type)
    {
        return isset($type['params'][0]) ? $type['params'][0] : $this->defaultFormat;
    }
}
