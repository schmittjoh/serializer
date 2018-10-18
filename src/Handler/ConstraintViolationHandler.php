<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\XmlSerializationVisitor;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

final class ConstraintViolationHandler implements SubscribingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        $methods = [];
        $formats = ['xml', 'json'];
        $types = ['Symfony\Component\Validator\ConstraintViolationList' => 'serializeList', 'Symfony\Component\Validator\ConstraintViolation' => 'serializeViolation'];

        foreach ($types as $type => $method) {
            foreach ($formats as $format) {
                $methods[] = [
                    'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                    'type' => $type,
                    'format' => $format,
                    'method' => $method . 'To' . $format,
                ];
            }
        }

        return $methods;
    }

    public function serializeListToXml(XmlSerializationVisitor $visitor, ConstraintViolationList $list, array $type): void
    {
        $currentNode = $visitor->getCurrentNode();
        if (!$currentNode) {
            $visitor->createRoot();
        }

        foreach ($list as $violation) {
            $this->serializeViolationToXml($visitor, $violation);
        }
    }

    /**
     * @return array|\ArrayObject
     */
    public function serializeListToJson(JsonSerializationVisitor $visitor, ConstraintViolationList $list, array $type, SerializationContext $context)
    {
        return $visitor->visitArray(iterator_to_array($list), $type);
    }

    public function serializeViolationToXml(XmlSerializationVisitor $visitor, ConstraintViolation $violation, ?array $type = null): void
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

    public function serializeViolationToJson(JsonSerializationVisitor $visitor, ConstraintViolation $violation, ?array $type = null): array
    {
        return [
            'property_path' => $violation->getPropertyPath(),
            'message' => $violation->getMessage(),
        ];
    }
}
