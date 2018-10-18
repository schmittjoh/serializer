<?php

declare(strict_types=1);

namespace JMS\Serializer;

interface NullAwareVisitorInterface
{
    /**
     * Determine if a value conveys a null value.
     * An example could be an xml element (Dom, SimpleXml, ...) that is tagged with a xsi:nil attribute
     *
     * @param mixed $value
     */
    public function isNull($value): bool;
}
