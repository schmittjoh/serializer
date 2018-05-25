<?php

namespace JMS\Serializer\Handler;

use JMS\Serializer\GraphNavigator;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\VisitorInterface;
use JMS\Serializer\XmlSerializationVisitor;
use JMS\Serializer\YamlSerializationVisitor;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Translation\TranslatorInterface;

class FormErrorHandler implements SubscribingHandlerInterface
{
    private $translator;

    private $translationDomain;

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

    public function __construct(TranslatorInterface $translator = null, $translationDomain = 'validators')
    {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
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

        if ($this->translator === null){
            return $error->getMessage();
        }

        if (null !== $error->getMessagePluralization()) {
            return $this->translator->transChoice($error->getMessageTemplate(), $error->getMessagePluralization(), $error->getMessageParameters(), $this->translationDomain);
        }

        return $this->translator->trans($error->getMessageTemplate(), $error->getMessageParameters(), $this->translationDomain);
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
