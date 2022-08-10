<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\TypedProperties\ConstructorPromotion;

use JMS\Serializer\Annotation as JMS;

class DefaultValuesAndAccessors
{
    public string $traditional = 'default';
    #[JMS\Accessor(setter: 'setTraditionalWithSetter')]
    public string $traditionalWithSetter = 'default';

    public function __construct(
        public string $promoted = 'default',
        #[JMS\Accessor(setter: 'setPromotedWithSetter')]
        public string $promotedWithSetter = 'default',
    ) {
    }

    public function setTraditionalWithSetter(string $value): void
    {
        $this->traditionalWithSetter = $value . '_fromsetter';
    }

    public function setPromotedWithSetter(string $value): void
    {
        $this->promotedWithSetter = $value . '_fromsetter';
    }
}
