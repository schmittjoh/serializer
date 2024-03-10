<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Handler;

use JMS\Serializer\Handler\JsonSerializableHandler;
use JMS\Serializer\Tests\Fixtures\Author;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use PHPUnit\Framework\TestCase;

final class JsonSerializableHandlerTest extends TestCase
{
    /** @var JsonSerializableHandler  */
    private $handler;

    public function testSerialize(): void
    {
        $data = new Author('scyzoryck');

        $serialized = ($this->handler)($this->createMock(SerializationVisitorInterface::class), $data);

        $this->assertEquals(['json_full_name' => 'scyzoryck'], $serialized);
    }

    protected function setUp(): void
    {
        $this->handler = new JsonSerializableHandler();
    }
}
