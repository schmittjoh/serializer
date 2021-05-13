<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Ordering;

use JMS\Serializer\Ordering\AlphabeticalPropertyOrderingStrategy;
use JMS\Serializer\Ordering\PropertyOrderingRegistry;
use PHPUnit\Framework\TestCase;

class PropertyOrderingRegistryTest extends TestCase
{
    /**
     * @var PropertyOrderingRegistry
     */
    private $registry;

    protected function setUp(): void
    {
        $this->registry = new PropertyOrderingRegistry();
    }

    public function testAdd(): void
    {
        $this->assertEquals([], $this->registry->all());

        $strategy = new AlphabeticalPropertyOrderingStrategy();

        $this->registry->add('MY_ALPHABETICAL', $strategy);
        $this->assertEquals(['MY_ALPHABETICAL' => $strategy], $this->registry->all());

        //Add twice with same name
        $this->registry->add('MY_ALPHABETICAL', $strategy);
        $this->assertEquals(['MY_ALPHABETICAL' => $strategy], $this->registry->all());

        $this->registry->add('MY_OTHER_ALPHABETICAL', $strategy);
        $this->assertEquals([
            'MY_ALPHABETICAL' => $strategy,
            'MY_OTHER_ALPHABETICAL' => $strategy,
        ], $this->registry->all());
    }

    public function testSupports(): void
    {
        $strategy = new AlphabeticalPropertyOrderingStrategy();
        $this->registry->add('MY_ALPHABETICAL', $strategy);

        $this->assertTrue($this->registry->supports('MY_ALPHABETICAL'));
        $this->assertFalse($this->registry->supports('NOT_DEFINED_NAME'));
    }

    public function testGet(): void
    {
        $strategy = new AlphabeticalPropertyOrderingStrategy();
        $this->registry->add('MY_ALPHABETICAL', $strategy);

        $this->assertEquals($strategy, $this->registry->get('MY_ALPHABETICAL'));
        $this->assertNull($this->registry->get('NOT_DEFINED_NAME'));
    }
}
