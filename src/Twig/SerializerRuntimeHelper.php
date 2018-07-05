<?php

declare(strict_types=1);

namespace JMS\Serializer\Twig;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

final class SerializerRuntimeHelper
{
    protected $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function serialize($object, string $type = 'json', ?SerializationContext $context = null): string
    {
        return $this->serializer->serialize($object, $type, $context);
    }
}
