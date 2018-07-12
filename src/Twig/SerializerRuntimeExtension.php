<?php

declare(strict_types=1);

namespace JMS\Serializer\Twig;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
final class SerializerRuntimeExtension extends \Twig_Extension
{
    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     *
     * @return string
     */
    public function getName()
    {
        return 'jms_serializer';
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     *
     * @return \Twig_Filter[]
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('serialize', [SerializerRuntimeHelper::class, 'serialize']),
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
}
