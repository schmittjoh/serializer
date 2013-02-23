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
use JMS\Serializer\YamlSerializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\GraphNavigator;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use JMS\Serializer\XmlSerializationVisitor;

class ConstraintViolationHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        $methods = array();
        $formats = array('xml', 'json', 'yml');
        $types = array('Symfony\Component\Validator\ConstraintViolationList' => 'serializeList', 'Symfony\Component\Validator\ConstraintViolation' => 'serializeViolation');

        foreach ($types as $type => $method) {
            foreach ($formats as $format) {
                $methods[] = array(
                    'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                    'type' => $type,
                    'format' => $format,
                    'method' => $method.'To'.$format,
                );
            }
        }

        return $methods;
    }

    public function serializeListToXml(XmlSerializationVisitor $visitor, ConstraintViolationList $list, array $type)
    {
        if (null === $visitor->document) {
            $visitor->document = $visitor->createDocument();
        }

        foreach ($list as $violation) {
            $this->serializeViolationToXml($visitor, $violation);
        }
    }

    public function serializeListToJson(JsonSerializationVisitor $visitor, ConstraintViolationList $list, array $type, Context $context)
    {
        return $visitor->visitArray(iterator_to_array($list), $type, $context);
    }

    public function serializeListToYml(YamlSerializationVisitor $visitor, ConstraintViolationList $list, array $type, Context $context)
    {
        return $visitor->visitArray(iterator_to_array($list), $type, $context);
    }

    public function serializeViolationToXml(XmlSerializationVisitor $visitor, ConstraintViolation $violation, array $type = null)
    {
        if (null === $visitor->document) {
            $visitor->document = $visitor->createDocument(null, null, false);
            $visitor->document->appendChild($violationNode = $visitor->document->createElement('violation'));
            $visitor->setCurrentNode($violationNode);
        } else {
            $visitor->getCurrentNode()->appendChild(
                $violationNode = $visitor->document->createElement('violation')
            );
        }

        $violationNode->setAttribute('property_path', $violation->getPropertyPath());
        $violationNode->appendChild($messageNode = $visitor->document->createElement('message'));

        $messageNode->appendChild($visitor->document->createCDATASection($violation->getMessage()));
    }

    public function serializeViolationToJson(JsonSerializationVisitor $visitor, ConstraintViolation $violation, array $type = null)
    {
        $data = array(
            'property_path' => $violation->getPropertyPath(),
            'message' => $violation->getMessage()
        );

        if (null === $visitor->getRoot()) {
            $visitor->setRoot($data);
        }

        return $data;
    }

    public function serializeViolationToYml(YamlSerializationVisitor $visitor, ConstraintViolation $violation, array $type = null)
    {
        return array(
            'property_path' => $violation->getPropertyPath(),
            'message' => $violation->getMessage(),
        );
    }
}
