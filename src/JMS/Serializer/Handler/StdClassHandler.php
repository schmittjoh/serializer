<?php

namespace JMS\Serializer\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\VisitorInterface;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
class StdClassHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        $methods = array();
        $formats = array('json', 'xml', 'yml');

        foreach ($formats as $format) {
            $methods[] = array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => 'stdClass',
                'format' => $format,
                'method' => 'serializeStdClass',
            );
        }

        return $methods;
    }

    public function serializeStdClass(VisitorInterface $visitor, \stdClass $stdClass, array $type, Context $context)
    {
        $classMetadata = $context->getMetadataFactory()->getMetadataForClass('stdClass');
        $visitor->startVisitingObject($classMetadata, $stdClass, array('name' => 'stdClass'), $context);

        foreach ((array)$stdClass as $name => $value) {
            $metadata = new StaticPropertyMetadata('stdClass', $name, $value);
            $visitor->visitProperty($metadata, $value, $context);
        }

        return $visitor->endVisitingObject($classMetadata, $stdClass, array('name' => 'stdClass'), $context);
    }
}
