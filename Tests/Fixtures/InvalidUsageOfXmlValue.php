<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

use JMS\SerializerBundle\Annotation\XmlValue;

/** Dummy */
class InvalidUsageOfXmlValue
{
    /** @XmlValue */
    private $value = 'bar';

    private $element = 'foo';
}