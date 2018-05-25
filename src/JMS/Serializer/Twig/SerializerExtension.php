<?php

namespace JMS\Serializer\Twig;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

/**
 * Serializer helper twig extension
 *
 * Basically provides access to JMSSerializer from Twig
 */
class SerializerExtension extends \Twig_Extension
{
    protected $serializer;

    public function getName()
    {
        return 'jms_serializer';
    }

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('serialize', array($this, 'serialize')),
        );
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('serialization_context', '\JMS\Serializer\SerializationContext::create'),
        );
    }

    /**
     * @param object $object
     * @param string $type
     * @param SerializationContext $context
     */
    public function serialize($object, $type = 'json', SerializationContext $context = null)
    {
        return $this->serializer->serialize($object, $type, $context);
    }
}
