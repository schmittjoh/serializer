<?php

declare(strict_types=1);

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
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     * @return string
     */
    public function getName()
    {
        return 'jms_serializer';
    }

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     *
     * @return \Twig_Filter[]
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('serialize', [$this, 'serialize']),
        ];
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     *
     * @return \Twig_Function[]
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('serialization_context', '\JMS\Serializer\SerializationContext::create'),
        ];
    }

    public function serialize(object $object, string $type = 'json', ?SerializationContext $context = null): string
    {
        return $this->serializer->serialize($object, $type, $context);
    }
}
