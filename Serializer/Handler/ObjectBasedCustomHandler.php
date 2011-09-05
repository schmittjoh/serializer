<?php

namespace JMS\SerializerBundle\Serializer\Handler;

use Metadata\MetadataFactoryInterface;

use JMS\SerializerBundle\Serializer\VisitorInterface;
use JMS\SerializerBundle\Serializer\Construction\ObjectConstructorInterface;

class ObjectBasedCustomHandler implements SerializationHandlerInterface, DeserializationHandlerInterface
{
    private $objectConstructor;
    private $metadataFactory;

    public function __construct(ObjectConstructorInterface $objectConstructor, MetadataFactoryInterface $metadataFactory)
    {
        $this->objectConstructor = $objectConstructor;
        $this->metadataFactory = $metadataFactory;
    }

    public function serialize(VisitorInterface $visitor, $data, $type, &$handled)
    {
        if (!$data instanceof SerializationHandlerInterface) {
            return;
        }

        return $data->serialize($visitor, $data, $type, $handled);
    }

    public function deserialize(VisitorInterface $visitor, $data, $type, &$handled)
    {
        if (!class_exists($type)
            || !in_array('JMS\SerializerBundle\Serializer\Handler\DeserializationHandlerInterface', class_implements($type))
        ) {
            return;
        }

        $metadata = $this->metadataFactory->getMetadataForClass($type);
        $visitor->startVisitingObject($metadata, $data, $type);

        $instance = $visitor->getResult();
        $instance->deserialize($visitor, $data, $type, $handled);

        return $instance;
    }
}
