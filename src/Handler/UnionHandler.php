<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\NonFloatCastableTypeException;
use JMS\Serializer\Exception\NonIntCastableTypeException;
use JMS\Serializer\Exception\NonStringCastableTypeException;
use JMS\Serializer\Exception\NonVisitableTypeException;
use JMS\Serializer\Exception\PropertyMissingException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\SerializationVisitorInterface;

final class UnionHandler implements SubscribingHandlerInterface
{
    private static $aliases = ['boolean' => 'bool', 'integer' => 'int', 'double' => 'float'];
    private bool $requireAllProperties;

    public function __construct(bool $requireAllProperties = false)
    {
        $this->requireAllProperties = $requireAllProperties;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        $methods = [];
        $formats = ['json', 'xml'];

        foreach ($formats as $format) {
            $methods[] = [
                'type' => 'union',
                'format' => $format,
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'method' => 'deserializeUnion',
            ];
            $methods[] = [
                'type' => 'union',
                'format' => $format,
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'method' => 'serializeUnion',
            ];
        }

        return $methods;
    }

    public function serializeUnion(
        SerializationVisitorInterface $visitor,
        mixed $data,
        array $type,
        SerializationContext $context
    ): mixed {
        if ($this->isPrimitiveType(gettype($data))) {
            return $this->matchSimpleType($data, $type, $context);
        } else {
            $resolvedType = [
                'name' => get_class($data),
                'params' => [],
            ];

            return $context->getNavigator()->accept($data, $resolvedType);
        }
    }

    public function deserializeUnion(DeserializationVisitorInterface $visitor, mixed $data, array $type, DeserializationContext $context): mixed
    {
        if ($data instanceof \SimpleXMLElement) {
            throw new RuntimeException('XML deserialisation into union types is not supported yet.');
        }

        $alternativeName = null;

        foreach ($type['params'] as $possibleType) {
            $propertyMetadata = $context->getMetadataStack()->top();
            $finalType = null;
            if (null !== $propertyMetadata->unionDiscriminatorField) {
                if (!array_key_exists($propertyMetadata->unionDiscriminatorField, $data)) {
                    throw new NonVisitableTypeException('Union Discriminator Field \'' . $propertyMetadata->unionDiscriminatorField . '\' not found in data');
                }

                $lkup = $data[$propertyMetadata->unionDiscriminatorField];
                if (!empty($propertyMetadata->unionDiscriminatorMap)) {
                    if (array_key_exists($lkup, $propertyMetadata->unionDiscriminatorMap)) {
                        $finalType = [
                            'name' => $propertyMetadata->unionDiscriminatorMap[$lkup],
                            'params' => [],
                        ];
                    } else {
                        throw new NonVisitableTypeException('Union Discriminator Map does not contain key \'' . $lkup . '\'');
                    }
                } else {
                    $finalType = [
                        'name' => $lkup,
                        'params' => [],
                    ];
                }
            }

            if (null !== $finalType && null !== $finalType['name']) {
                return $context->getNavigator()->accept($data, $finalType);
            } else {
                try {
                    $previousVisitorRequireSetting = $visitor->getRequireAllRequiredProperties();
                    if ($this->requireAllProperties) {
                        $visitor->setRequireAllRequiredProperties($this->requireAllProperties);
                    }

                    if ($this->isPrimitiveType($possibleType['name']) && (is_array($data) || !$this->testPrimitive($data, $possibleType['name'], $context->getFormat()))) {
                        continue;
                    }

                    $accept = $context->getNavigator()->accept($data, $possibleType);
                    if ($this->requireAllProperties) {
                        $visitor->setRequireAllRequiredProperties($previousVisitorRequireSetting);
                    }

                    return $accept;
                } catch (NonVisitableTypeException $e) {
                    continue;
                } catch (PropertyMissingException $e) {
                    continue;
                } catch (NonStringCastableTypeException $e) {
                    continue;
                } catch (NonIntCastableTypeException $e) {
                    continue;
                } catch (NonFloatCastableTypeException $e) {
                    continue;
                }
            }
        }

        return null;
    }

    private function matchSimpleType(mixed $data, array $type, Context $context): mixed
    {
        $alternativeName = null;

        foreach ($type['params'] as $possibleType) {
            if ($this->isPrimitiveType($possibleType['name']) && !$this->testPrimitive($data, $possibleType['name'], $context->getFormat())) {
                continue;
            }

            try {
                return $context->getNavigator()->accept($data, $possibleType);
            } catch (NonVisitableTypeException $e) {
                continue;
            } catch (PropertyMissingException $e) {
                continue;
            }
        }

        return null;
    }

    private function isPrimitiveType(string $type): bool
    {
        return in_array($type, ['int', 'integer', 'float', 'double', 'bool', 'boolean', 'string']);
    }

    private function testPrimitive(mixed $data, string $type, string $format): bool
    {
        switch ($type) {
            case 'integer':
            case 'int':
                return (string) (int) $data === (string) $data;

            case 'double':
            case 'float':
                return (string) (float) $data === (string) $data;

            case 'bool':
            case 'boolean':
                return (string) (bool) $data === (string) $data;

            case 'string':
                return (string) $data === (string) $data;
        }

        return false;
    }
}
