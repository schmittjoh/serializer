<?php

declare(strict_types=1);

namespace JMS\Serializer\Visitor\Factory;

use JMS\Serializer\Visitor\DeserializationVisitorInterface;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
interface DeserializationVisitorFactory
{
    public function getVisitor(): DeserializationVisitorInterface;
}
