<?php

namespace JMS\Serializer\Twig;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
final class SerializerRuntimeExtension extends \Twig_Extension
{

    public function getName()
    {
        return 'jms_serializer';
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('serialize', array(SerializerRuntimeHelper::class, 'serialize')),
        );
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('serialization_context', '\JMS\Serializer\SerializationContext::create'),
        );
    }
}
