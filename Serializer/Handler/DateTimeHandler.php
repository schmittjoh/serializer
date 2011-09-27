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

use Symfony\Component\Yaml\Inline;

use JMS\SerializerBundle\Serializer\YamlSerializationVisitor;

use JMS\SerializerBundle\Serializer\XmlDeserializationVisitor;

use JMS\SerializerBundle\Serializer\GenericDeserializationVisitor;

use JMS\SerializerBundle\Exception\RuntimeException;
use JMS\SerializerBundle\Serializer\GenericSerializationVisitor;
use JMS\SerializerBundle\Serializer\JsonSerializationVisitor;
use JMS\SerializerBundle\Serializer\XmlSerializationVisitor;
use JMS\SerializerBundle\Serializer\VisitorInterface;

class DateTimeHandler implements SerializationHandlerInterface, DeserializationHandlerInterface
{
    private $format;
    private $defaultTimezone;

    public function __construct($format = \DateTime::ISO8601, $defaultTimezone = 'UTC')
    {
        $this->format = $format;
        $this->defaultTimezone = new \DateTimeZone($defaultTimezone);
    }

    public function serialize(VisitorInterface $visitor, $data, $type, &$visited)
    {
        if (!$data instanceof \DateTime) {
            return;
        }

        if ($visitor instanceof XmlSerializationVisitor) {
            if (null === $visitor->document) {
                $visitor->document = $visitor->createDocument(null, null, true);
            }
            $visited = true;

            return $visitor->document->createTextNode($data->format($this->format));
        } else if ($visitor instanceof GenericSerializationVisitor) {
            $visited = true;

            return $data->format($this->format);
        } else if ($visitor instanceof YamlSerializationVisitor) {
            $visited = true;

            return Inline::dump($data->format($this->format));
        }
    }

    public function deserialize(VisitorInterface $visitor, $data, $type, &$visited)
    {
        if ('DateTime' !== $type) {
            return;
        }

        if ($visitor instanceof GenericDeserializationVisitor || $visitor instanceof XmlDeserializationVisitor) {
            $datetime = \DateTime::createFromFormat($this->format, (string) $data, $this->defaultTimezone);
            if (false === $datetime) {
                throw new RuntimeException(sprintf('Invalid datetime "%s", expected format %s.', $data, $this->format));
            }

            $visited = true;

            return $datetime;
        }
    }
}