<?php

declare(strict_types=1);

namespace JMS\Serializer\Twig;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Serializer helper twig extension
 *
 * Basically provides access to JMSSerializer from Twig
 */
class SerializerExtension extends AbstractExtension
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @return string
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
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
     * @return TwigFilter[]
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     */
    public function getFilters()
    {
        return [
            new TwigFilter('serialize', [$this, 'serialize']),
        ];
    }

    /**
     * @return TwigFunction[]
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('serialization_context', '\JMS\Serializer\SerializationContext::create'),
        ];
    }

    public function serialize(object $object, string $type = 'json', ?SerializationContext $context = null): string
    {
        return $this->serializer->serialize($object, $type, $context);
    }
}
