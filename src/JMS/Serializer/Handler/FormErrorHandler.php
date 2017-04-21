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

use JMS\Serializer\YamlSerializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\VisitorInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Translation\TranslatorInterface;
use JMS\Serializer\XmlSerializationVisitor;

class FormErrorHandler implements SubscribingHandlerInterface
{
    private $translator;

    public static function getSubscribingMethods()
    {
        $methods = array();
        foreach (array('xml', 'json', 'yml') as $format) {
            $methods[] = array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => 'Symfony\Component\Form\Form',
                'format' => $format,
            );
            $methods[] = array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => 'Symfony\Component\Form\FormError',
                'format' => $format,
            );
        }

        return $methods;
    }

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function serializeFormToXml(XmlSerializationVisitor $visitor, Form $form, array $type)
    {
        if (null === $visitor->document) {
            $visitor->document = $visitor->createDocument(null, null, false);
            $visitor->document->appendChild($formNode = $visitor->document->createElement('form'));
            $visitor->setCurrentNode($formNode);
        } else {
            $visitor->getCurrentNode()->appendChild(
                $formNode = $visitor->document->createElement('form')
            );
        }

        $formNode->setAttribute('name', $form->getName());

        $formNode->appendChild($errorsNode = $visitor->document->createElement('errors'));
        foreach ($form->getErrors() as $error) {
            $errorNode = $visitor->document->createElement('entry');
            $errorNode->appendChild($this->serializeFormErrorToXml($visitor, $error, array()));
            $errorsNode->appendChild($errorNode);
        }

        foreach ($form->all() as $child) {
            if ($child instanceof Form) {
                if (null !== $node = $this->serializeFormToXml($visitor, $child, array())) {
                    $formNode->appendChild($node);
                }
            }
        }

        return $formNode;
    }

    public function serializeFormToJson(JsonSerializationVisitor $visitor, Form $form, array $type)
    {
        return $this->convertFormToArray($visitor, $form);
    }

    public function serializeFormToYml(YamlSerializationVisitor $visitor, Form $form, array $type)
    {
        return $this->convertFormToArray($visitor, $form);
    }

    public function serializeFormErrorToXml(XmlSerializationVisitor $visitor, FormError $formError, array $type)
    {
        if (null === $visitor->document) {
            $visitor->document = $visitor->createDocument(null, null, true);
        }

        return $visitor->document->createCDATASection($this->getErrorMessage($formError));
    }

    public function serializeFormErrorToJson(JsonSerializationVisitor $visitor, FormError $formError, array $type)
    {
        return $this->getErrorMessage($formError);
    }

    public function serializeFormErrorToYml(YamlSerializationVisitor $visitor, FormError $formError, array $type)
    {
        return $this->getErrorMessage($formError);
    }

    private function getErrorMessage(FormError $error)
    {
        if (null !== $error->getMessagePluralization()) {
            return $this->translator->transChoice($error->getMessageTemplate(), $error->getMessagePluralization(), $error->getMessageParameters(), 'validators');
        }

        return $this->translator->trans($error->getMessageTemplate(), $error->getMessageParameters(), 'validators');
    }

    private function convertFormToArray(VisitorInterface $visitor, Form $data)
    {
        $isRoot = null === $visitor->getRoot();

        $form = new \ArrayObject();
        $errors = array();
        foreach ($data->getErrors() as $error) {
            $errors[] = $this->getErrorMessage($error);
        }

        if ($errors) {
            $form['errors'] = $errors;
        }

        $children = array();
        foreach ($data->all() as $child) {
            if ($child instanceof Form) {
                $children[$child->getName()] = $this->convertFormToArray($visitor, $child);
            }
        }

        if ($children) {
            $form['children'] = $children;
        }

        if ($isRoot) {
            $visitor->setRoot($form);
        }

        return $form;
    }
}
