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

use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\XmlSerializationVisitor;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

final class ConstraintViolationHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        $methods = array();
        $formats = array('xml', 'json');
        $types = array('Symfony\Component\Validator\ConstraintViolationList' => 'serializeList', 'Symfony\Component\Validator\ConstraintViolation' => 'serializeViolation');

        foreach ($types as $type => $method) {
            foreach ($formats as $format) {
                $methods[] = array(
                    'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                    'type' => $type,
                    'format' => $format,
                    'method' => $method . 'To' . $format,
                );
            }
        }

        return $methods;
    }

    public function serializeListToXml(XmlSerializationVisitor $visitor, ConstraintViolationList $list, array $type)
    {
        $currentNode = $visitor->getCurrentNode();
        if (!$currentNode) {
            $visitor->createRoot();
        }

        foreach ($list as $violation) {
            $this->serializeViolationToXml($visitor, $violation);
        }
    }

    public function serializeListToJson(JsonSerializationVisitor $visitor, ConstraintViolationList $list, array $type, SerializationContext $context)
    {
        return $visitor->visitArray(iterator_to_array($list), $type);
    }

    public function serializeViolationToXml(XmlSerializationVisitor $visitor, ConstraintViolation $violation, array $type = null)
    {
        $violationNode = $visitor->getDocument()->createElement('violation');

        $parent = $visitor->getCurrentNode();
        if (!$parent) {
            $visitor->setCurrentAndRootNode($violationNode);
        } else {
            $parent->appendChild($violationNode);
        }

        $violationNode->setAttribute('property_path', $violation->getPropertyPath());
        $violationNode->appendChild($messageNode = $visitor->getDocument()->createElement('message'));

        $messageNode->appendChild($visitor->getDocument()->createCDATASection($violation->getMessage()));
    }

    public function serializeViolationToJson(JsonSerializationVisitor $visitor, ConstraintViolation $violation, array $type = null)
    {
        $data = array(
            'property_path' => $violation->getPropertyPath(),
            'message' => $violation->getMessage()
        );

        return $data;
    }
}
