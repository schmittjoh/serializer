<?php

namespace JMS\SerializerBundle\Serializer\Handler;

use Metadata\MetadataFactoryInterface;

use JMS\SerializerBundle\Serializer\VisitorInterface;
use JMS\SerializerBundle\Serializer\Construction\ObjectConstructorInterface;

class ObjectBasedCustomHandler implements SerializationHandlerInterface, DeserializationHandlerInterface
{
    private $objectConstructor;
    private $metadataFactory;

    public function __construct(ObjectConstructorInterface $constructor, MetadataFactoryInterface $metadata)
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
        if (!is_a($type, 'JMS\SerializerBundle\Serializer\Handler\DeserializationHandlerInterface')) {
            return;
        }

        $metadata = $this->metadataFactory->getMetadataForClass($type);

        $instance = $this->objectConstructor->construct($visitor, $metadata, $data, $type);
        $instance->deserialize($visitor, $data, $type, $handled);

        return $instance;
    }
}