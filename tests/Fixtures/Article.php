<?php

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
