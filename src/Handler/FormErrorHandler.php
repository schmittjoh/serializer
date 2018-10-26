<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use JMS\Serializer\XmlSerializationVisitor;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Translation\TranslatorInterface;

final class FormErrorHandler implements SubscribingHandlerInterface
{
    /**
     * @var TranslatorInterface|null
     */
    private $translator;

    /**
     * @var string
     */
    private $translationDomain;


    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        $methods = [];
        foreach (['xml', 'json'] as $format) {
            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'type' => 'Symfony\Component\Form\Form',
                'format' => $format,
            ];
            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'type' => 'Symfony\Component\Form\FormError',
                'format' => $format,
            ];
        }

        return $methods;
    }

    public function __construct(?TranslatorInterface $translator = null, string $translationDomain = 'validators')
    {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    /**
     * @param array $type
     */
    public function serializeFormToXml(XmlSerializationVisitor $visitor, Form $form, array $type): \DOMElement
    {
        $formNode = $visitor->getDocument()->createElement('form');

        $formNode->setAttribute('name', $form->getName());

        $formNode->appendChild($errorsNode = $visitor->getDocument()->createElement('errors'));
        foreach ($form->getErrors() as $error) {
            $errorNode = $visitor->getDocument()->createElement('entry');
            $errorNode->appendChild($this->serializeFormErrorToXml($visitor, $error, []));
            $errorsNode->appendChild($errorNode);
        }

        foreach ($form->all() as $child) {
            if ($child instanceof Form) {
                if (null !== $node = $this->serializeFormToXml($visitor, $child, [])) {
                    $formNode->appendChild($node);
                }
            }
        }

        return $formNode;
    }

    /**
     * @param array $type
     */
    public function serializeFormToJson(JsonSerializationVisitor $visitor, Form $form, array $type): \ArrayObject
    {
        return $this->convertFormToArray($visitor, $form);
    }

    /**
     * @param array $type
     */
    public function serializeFormErrorToXml(XmlSerializationVisitor $visitor, FormError $formError, array $type): \DOMCdataSection
    {
        return $visitor->getDocument()->createCDATASection($this->getErrorMessage($formError));
    }

    /**
     * @param array $type
     */
    public function serializeFormErrorToJson(JsonSerializationVisitor $visitor, FormError $formError, array $type): string
    {
        return $this->getErrorMessage($formError);
    }

    private function getErrorMessage(FormError $error): ?string
    {
        if (null === $this->translator) {
            return $error->getMessage();
        }

        if (null !== $error->getMessagePluralization()) {
            return $this->translator->transChoice($error->getMessageTemplate(), $error->getMessagePluralization(), $error->getMessageParameters(), $this->translationDomain);
        }

        return $this->translator->trans($error->getMessageTemplate(), $error->getMessageParameters(), $this->translationDomain);
    }

    private function convertFormToArray(SerializationVisitorInterface $visitor, Form $data): \ArrayObject
    {
        $form = new \ArrayObject();
        $errors = [];
        foreach ($data->getErrors() as $error) {
            $errors[] = $this->getErrorMessage($error);
        }

        if ($errors) {
            $form['errors'] = $errors;
        }

        $children = [];
        foreach ($data->all() as $child) {
            if ($child instanceof Form) {
                $children[$child->getName()] = $this->convertFormToArray($visitor, $child);
            }
        }

        if ($children) {
            $form['children'] = $children;
        }

        return $form;
    }
}
