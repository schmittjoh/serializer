<?php

namespace JMS\SerializerBundle\Serializer\Handler;

use JMS\SerializerBundle\Serializer\Handler\SerializationHandlerInterface;
use JMS\SerializerBundle\Serializer\VisitorInterface;
use JMS\SerializerBundle\Serializer\XmlSerializationVisitor;
use JMS\SerializerBundle\Serializer\GenericSerializationVisitor;
use JMS\SerializerBundle\Serializer\YamlSerializationVisitor;
use Symfony\Component\Yaml\Inline;

class NullHandler implements SerializationHandlerInterface
{
    public function serialize(VisitorInterface $visitor, $data, $type, &$handled)
    {
        if ($data !== null) {
            return;
        }

        if ($visitor instanceof XmlSerializationVisitor) {

            if (null === $visitor->document) {
                $visitor->document = $visitor->createDocument(null, null, true);
            }
            $handled = true;

            $attr = $visitor->document->createAttribute('xsi:nil');
            $attr->value = 'true';
            return $attr;

        }
        if ($visitor instanceof GenericSerializationVisitor) {

            $handled = true;
            return null;

        }
        if ($visitor instanceof YamlSerializationVisitor) {

            $handled = true;
            return Inline::dump(null);

        }
    }
}
