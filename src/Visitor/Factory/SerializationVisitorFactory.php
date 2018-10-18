<?php

declare(strict_types=1);

namespace JMS\Serializer\Visitor\Factory;

use JMS\Serializer\Visitor\SerializationVisitorInterface;

/**
 * @author Asmir Mustafic <goetas@gmail.com>
 */
interface SerializationVisitorFactory
{
    public function getVisitor(): SerializationVisitorInterface;
}
