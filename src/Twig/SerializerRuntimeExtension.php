<?php

declare(strict_types=1);

namespace JMS\Serializer\Twig;

use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
final class SerializerRuntimeExtension extends SerializerBaseExtension
{
    /**
     * @return TwigFilter[]
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     */
    public function getFilters()
    {
        return [
            new TwigFilter($this->serializationFunctionsPrefix . 'serialize', [SerializerRuntimeHelper::class, 'serialize']),
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
            new TwigFunction($this->serializationFunctionsPrefix . 'serialization_context', '\JMS\Serializer\SerializationContext::create'),
        ];
    }
}
