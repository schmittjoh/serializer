<?php

declare(strict_types=1);

namespace JMS\Serializer\Twig;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
final class SerializerRuntimeExtension extends \Twig_Extension
{
    /**
     * @return string
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     */
    public function getName()
    {
        return 'jms_serializer';
    }

    /**
     * @return \Twig_Filter[]
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('serialize', [SerializerRuntimeHelper::class, 'serialize']),
        ];
    }

    /**
     * @return \Twig_Function[]
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('serialization_context', '\JMS\Serializer\SerializationContext::create'),
        ];
    }
}
