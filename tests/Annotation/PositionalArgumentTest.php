<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Annotation;

use JMS\Serializer\Annotation\AccessorOrder;
use JMS\Serializer\Annotation\AccessType;
use JMS\Serializer\Annotation\XmlNamespace;
use PHPUnit\Framework\TestCase;

class PositionalArgumentTest extends TestCase
{
    public function testAccessType()
    {
        self::assertSame('public_method', (new AccessType('public_method'))->type);
        self::assertSame('public_method', (new AccessType(['value' => 'public_method']))->type);
    }

    public function testAccessorOrder()
    {
        self::assertSame('alphabetical', (new AccessorOrder('alphabetical'))->order);
        self::assertSame('alphabetical', (new AccessorOrder(['value' => 'alphabetical']))->order);
    }

    public function testXmlNamespace()
    {
        self::assertSame('http://example.com/ns', (new XmlNamespace('http://example.com/ns'))->uri);
        self::assertSame('http://example.com/ns', (new XmlNamespace(['value' => 'http://example.com/ns']))->uri);
    }
}
