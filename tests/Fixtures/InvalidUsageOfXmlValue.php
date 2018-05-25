<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\XmlValue;

/** Dummy */
class InvalidUsageOfXmlValue
{
    /** @XmlValue */
    private $value = 'bar';

    private $element = 'foo';
}
