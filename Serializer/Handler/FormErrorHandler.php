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

use JMS\SerializerBundle\Serializer\GenericSerializationVisitor;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Translation\TranslatorInterface;
use JMS\SerializerBundle\Serializer\Handler\SerializationHandlerInterface;
use JMS\SerializerBundle\Serializer\VisitorInterface;
use JMS\SerializerBundle\Serializer\XmlSerializationVisitor;

class FormErrorHandler implements SerializationHandlerInterface
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function serialize(VisitorInterface $visitor, $data, $type, &$handled)
    {
        if ($data instanceof Form) {
            if ($visitor instanceof XmlSerializationVisitor) {
                $handled = true;

                if (null === $visitor->document) {
                    $visitor->document = $visitor->createDocument(null, null, false);
                    $visitor->document->appendChild($formNode = $visitor->document->createElement('form'));
                    $visitor->setCurrentNode($formNode);
                } else {
                    $visitor->getCurrentNode()->appendChild(
                        $formNode = $visitor->document->createElement('form')
                    );
                }

                $formNode->setAttribute('name', $data->getName());

                $formNode->appendChild($errorsNode = $visitor->document->createElement('errors'));
                foreach ($data->getErrors() as $error) {
                    $errorNode = $visitor->document->createElement('entry');
                    $errorNode->appendChild($this->serialize($visitor, $error, null, $visited));
                    $errorsNode->appendChild($errorNode);
                }

                foreach ($data->getChildren() as $child) {
                    if (null !== $node = $this->serialize($visitor, $child, null, $visited)) {
                        $formNode->appendChild($node);
                    }
                }

                return;
            } else if ($visitor instanceof GenericSerializationVisitor) {
                $handled = true;
                $isRoot = null === $visitor->getRoot();

                $form = $errors = array();
                foreach ($data->getErrors() as $error) {
                    $errors[] = $this->serialize($visitor, $error, null, $visited);
                }

                if ($errors) {
                    $form['errors'] = $errors;
                }

                $children = array();
                foreach ($data->getChildren() as $child) {
                    $children[$child->getName()] = $this->serialize($visitor, $child, null, $visited);
                }

                if ($children) {
                    $form['children'] = $children;
                }

                if ($isRoot) {
                    $visitor->setRoot($form);
                }

                return $form;
            }
        } else if ($data instanceof FormError) {
            $handled = true;
            $message = $this->translator->trans($data->getMessageTemplate(), $data->getMessageParameters(), 'validators');

            if ($visitor instanceof XmlSerializationVisitor) {
                if (null === $visitor->document) {
                    $visitor->document = $visitor->createDocument(null, null, true);
                }

                return $visitor->document->createCDATASection($message);
            }

            return $message;
        }

        return null;
    }
}