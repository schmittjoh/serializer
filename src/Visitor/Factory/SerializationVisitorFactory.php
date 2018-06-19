<?php

declare(strict_types=1);

namespace JMS\Serializer\Visitor\Factory;

use JMS\Serializer\Visitor\SerializationVisitorInterface;

interface SerializationVisitorFactory
{
    public function getVisitor(): SerializationVisitorInterface;
}
