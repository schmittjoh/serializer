<?php

namespace JMS\Serializer\Construction;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\VisitorInterface;
use Psr\Container\ContainerInterface;

/**
 * Container object constructor for new (or existing) objects during deserialization.
 */
class ContainerObjectConstructor implements ObjectConstructorInterface
{
    private $container;
    private $fallbackConstructor;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     * @param ObjectConstructorInterface $fallbackConstructor
     */
    public function __construct(ContainerInterface $container, ObjectConstructorInterface $fallbackConstructor)
    {
        $this->container = $container;
        $this->fallbackConstructor = $fallbackConstructor;
    }

    /**
     * {@inheritdoc}
     */
    public function construct(VisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context)
    {
        if ($this->container->has($metadata->name)) {
            $object = $this->container->get($metadata->name);
        } elseif ($this->fallbackConstructor) {
            // No ObjectManager found, proceed with normal deserialization
            $object = $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        } else {
            $object = null;
        }

        return $object;
    }
}
