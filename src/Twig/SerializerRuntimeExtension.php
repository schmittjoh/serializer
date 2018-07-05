<?php

declare(strict_types=1);

namespace JMS\Serializer\Twig;

final class SerializerRuntimeExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'jms_serializer';
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('serialize', [SerializerRuntimeHelper::class, 'serialize']),
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('serialization_context', '\JMS\Serializer\SerializationContext::create'),
        ];
    }
}
