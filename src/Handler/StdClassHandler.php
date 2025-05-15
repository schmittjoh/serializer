<?php

declare(strict_types=1);

namespace JMS\Serializer\Handler;

use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Type\Type;
use JMS\Serializer\Visitor\SerializationVisitorInterface;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 *
 * @phpstan-import-type TypeArray from Type
 */
final class StdClassHandler implements SubscribingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods(): iterable
    {
        $methods = [];
        $formats = ['json', 'xml'];

        foreach ($formats as $format) {
            $methods[] = [
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'type' => \stdClass::class,
                'format' => $format,
                'method' => 'serializeStdClass',
            ];
        }

        return $methods;
    }

    /**
     * @param TypeArray $type
     *
     * @return mixed
     */
    public function serializeStdClass(SerializationVisitorInterface $visitor, \stdClass $stdClass, array $type, SerializationContext $context)
    {
        $classMetadata = $context->getMetadataFactory()->getMetadataForClass('stdClass');
        $visitor->startVisitingObject($classMetadata, $stdClass, ['name' => 'stdClass']);

        foreach ((array) $stdClass as $name => $value) {
            $metadata = new StaticPropertyMetadata('stdClass', $name, $value);
            $visitor->visitProperty($metadata, $value);
        }

        return $visitor->endVisitingObject($classMetadata, $stdClass, ['name' => 'stdClass']);
    }
}
