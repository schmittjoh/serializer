<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;

class BackedEnumHandler implements SubscribingHandlerInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribingMethods(): array
    {
        $methods = [];
        foreach (['json', 'xml'] as $format) {
            $methods[] = [
                'type' => \BackedEnum::class,
                'format' => $format,
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'method' => 'serializeBackedEnum',
            ];

            $methods[] = [
                'type' => \BackedEnum::class,
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => $format,
                'method' => 'deserializeBackedEnum',
            ];
        }

        return $methods;
    }

    /**
     * @param SerializationVisitorInterface $visitor
     * @param \BackedEnum                   $object
     * @param array                         $type
     * @param SerializationContext          $context
     *
     * @return null|string|int
     */
    public function serializeBackedEnum(SerializationVisitorInterface $visitor, $object, array $type, Context $context)
    {
        $value = $object->value;
        if ('int' === $this->getValueType($type, $context)) {
            return $visitor->visitInteger($value, $type);
        }

        return $visitor->visitString($value, $type);
    }

    /**
     * @param DeserializationVisitorInterface $visitor
     * @param string|int                      $value
     * @param array                           $type
     * @param Context                         $context
     *
     * @return null|\BackedEnum
     */
    public function deserializeBackedEnum(DeserializationVisitorInterface $visitor, $value, array $type, Context $context)
    {
        if ('int' === $this->getValueType($type, $context)) {
            $value = $visitor->visitInteger($value, $type);
        } else {
            $value = $visitor->visitString($value, $type);
        }

        return $this->tryFrom($type, $value);
    }

    /**
     * @param array           $type
     * @param null|int|string $value
     *
     * @return null|\BackedEnum
     */
    protected function tryFrom(array $type, $value)
    {
        return $type['enum']::tryFrom($value);
    }

    protected function getValueType(array $type, Context $context): string
    {
        return $context->getMetadataFactory()->getMetadataForClass($type['enum'])->propertyMetadata['value']->type['name'];
    }
}
