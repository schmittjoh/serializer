<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

use JMS\Serializer\Exception\InvalidMetadataException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;

final class EnumHandler implements SubscribingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        $methods = [];

        foreach (['json', 'xml'] as $format) {
            $methods[] = [
                'type' => 'enum',
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => $format,
                'method' => 'deserializeEnum',
            ];
            $methods[] = [
                'type' => 'enum',
                'format' => $format,
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'method' => 'serializeEnum',
            ];
        }

        return $methods;
    }

    public function serializeEnum(
        SerializationVisitorInterface $visitor,
        \UnitEnum $enum,
        array $type,
        SerializationContext $context
    ) {
        if ((isset($type['params'][1]) && 'value' === $type['params'][1]) || (!isset($type['params'][1]) && $enum instanceof \BackedEnum)) {
            if (!$enum instanceof \BackedEnum) {
                throw new InvalidMetadataException(sprintf('The type "%s" is not a backed enum, thus you can not use "value" as serialization mode for its value.', get_class($enum)));
            }

            $valueType = isset($type['params'][2]) ? ['name' => $type['params'][2]] : null;

            return $context->getNavigator()->accept($enum->value, $valueType);
        } else {
            return $context->getNavigator()->accept($enum->name);
        }
    }

    /**
     * @param int|string|\SimpleXMLElement $data
     * @param array $type
     */
    public function deserializeEnum(DeserializationVisitorInterface $visitor, $data, array $type): ?\UnitEnum
    {
        $enumType = $type['params'][0];
        if (isset($enumType['name'])) {
            $enumType = $enumType['name'];
        } else {
            trigger_deprecation('jms/serializer', '3.31', "Using enum<'Type'> or similar is deprecated, use enum<Type> instead.");
        }

        $caseValue = (string) $data;

        $ref = new \ReflectionEnum($enumType);
        if (isset($type['params'][1]) && 'value' === $type['params'][1] || (!isset($type['params'][1]) && is_a($enumType, \BackedEnum::class, true))) {
            if (!is_a($enumType, \BackedEnum::class, true)) {
                throw new InvalidMetadataException(sprintf('The type "%s" is not a backed enum, thus you can not use "value" as serialization mode for its value.', $enumType));
            }

            if ('int' === $ref->getBackingType()->getName()) {
                if (!is_numeric($caseValue)) {
                    throw new RuntimeException(sprintf('"%s" is not a valid backing value for enum "%s"', $caseValue, $enumType));
                }

                $caseValue = (int) $caseValue;
            }

            return $enumType::from($caseValue);
        } else {
            if (!$ref->hasCase($caseValue)) {
                throw new InvalidMetadataException(sprintf('The type "%s" does not have the case "%s"', $ref->getName(), $caseValue));
            }

            return $ref->getCase($caseValue)->getValue();
        }
    }
}
