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

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\HandlerCallback;
use JMS\Serializer\Context;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\XmlDeserializationVisitor;
use JMS\Serializer\XmlSerializationVisitor;
use JMS\Serializer\YamlSerializationVisitor;
use Symfony\Component\Yaml\Inline;

class Article
{
    public $element;
    public $value;

    /** @HandlerCallback("xml", direction = "serialization") */
    public function serializeToXml(XmlSerializationVisitor $visitor, $data, Context $context)
    {
        if (null === $visitor->document) {
            $visitor->document = $visitor->createDocument(null, null, false);
        }

        $visitor->document->appendChild($visitor->document->createElement($this->element, $this->value));
    }

    /** @HandlerCallback("json", direction = "serialization") */
    public function serializeToJson(JsonSerializationVisitor $visitor)
    {
        $visitor->setRoot(array($this->element => $this->value));
    }

    /** @HandlerCallback("yml", direction = "serialization") */
    public function serializeToYml(YamlSerializationVisitor $visitor)
    {
        $visitor->writer->writeln(Inline::dump($this->element) . ': ' . Inline::dump($this->value));
    }

    /** @HandlerCallback("xml", direction = "deserialization") */
    public function deserializeFromXml(XmlDeserializationVisitor $visitor, \SimpleXMLElement $data)
    {
        $this->element = $data->getName();
        $this->value = (string)$data;
    }

    /** @HandlerCallback("json", direction = "deserialization") */
    public function deserializeFromJson(JsonDeserializationVisitor $visitor, array $data)
    {
        $this->element = key($data);
        $this->value = reset($data);
    }
}
