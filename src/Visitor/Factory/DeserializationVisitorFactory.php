<?php

declare(strict_types=1);

namespace JMS\Serializer\Visitor\Factory;

use JMS\Serializer\Visitor\DeserializationVisitorInterface;

interface DeserializationVisitorFactory
{
    public function getVisitor(): DeserializationVisitorInterface;
}
