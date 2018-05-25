<?php

namespace JMS\Serializer;

interface NullAwareVisitorInterface extends VisitorInterface
{
    /**
     * Determine if a value conveys a null value.
     * An example could be an xml element (Dom, SimpleXml, ...) that is tagged with a xsi:nil attribute
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function isNull($value);
}
