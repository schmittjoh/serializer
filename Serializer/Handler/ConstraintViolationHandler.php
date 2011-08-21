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

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use JMS\SerializerBundle\Serializer\Handler\SerializationHandlerInterface;
use JMS\SerializerBundle\Serializer\VisitorInterface;
use JMS\SerializerBundle\Serializer\GenericSerializationVisitor;
use JMS\SerializerBundle\Serializer\XmlSerializationVisitor;

class ConstraintViolationHandler implements SerializationHandlerInterface
{
    public function serialize(VisitorInterface $visitor, $data, $type, &$handled)
    {
        if ($data instanceof ConstraintViolationList) {
            if ($visitor instanceof XmlSerializationVisitor) {
                $handled = true;

                if (null === $visitor->document) {
                    $visitor->document = $visitor->createDocument();
                }

                foreach ($data as $violation) {
                    $this->serialize($visitor, $violation, null, $visited);
                }
            }
        } else if ($data instanceof ConstraintViolation) {
            if ($visitor instanceof XmlSerializationVisitor) {
                $handled = true;

                if (null === $visitor->document) {
                    $visitor->document = $visitor->createDocument(null, null, false);
                    $visitor->document->appendChild($violationNode = $visitor->document->createElement('violation'));
                    $visitor->setCurrentNode($violationNode);
                } else {
                    $visitor->getCurrentNode()->appendChild(
                        $violationNode = $visitor->document->createElement('violation')
                    );
                }

                $violationNode->setAttribute('property_path', $data->getPropertyPath());
                $violationNode->appendChild($messageNode = $visitor->document->createElement('message'));

                $messageNode->appendChild($visitor->document->createCDATASection($data->getMessage()));

                return;
            } else if ($visitor instanceof GenericSerializationVisitor) {
                $handled = true;

                $violation = array(
                    'property_path' => $data->getPropertyPath(),
                    'message' =>$data->getMessage()
                );

                if (null === $visitor->getRoot()) {
                    $visitor->setRoot($violation);
                }

                return $violation;
            }
        }

        return;
    }
}