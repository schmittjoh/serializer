<?php

namespace JMS\Serializer\Twig;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
final class SerializerRuntimeHelper
{
    protected $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param $object
     * @param string $type
     * @param SerializationContext|null $context
     * @return string
     */
    public function serialize($object, $type = 'json', SerializationContext $context = null)
    {
        return $this->serializer->serialize($object, $type, $context);
    }
}
