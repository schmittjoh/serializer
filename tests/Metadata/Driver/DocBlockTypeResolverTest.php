<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use JMS\Serializer\Metadata\Driver\DocBlockDriver\DocBlockTypeResolver;
use JMS\Serializer\Tests\Fixtures\ObjectWithPhpDocProperty;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

final class DocBlockTypeResolverTest extends TestCase
{
    public function testGetPropertyDocblockTypeHintDoesNotCrash(): void
    {
        // It crashed on PHP 7.3 and less because of array_merge(...[])

        $resolver = new DocBlockTypeResolver();
        self::assertNull(
            $resolver->getPropertyDocblockTypeHint(
                new ReflectionProperty(ObjectWithPhpDocProperty::class, 'emptyBlock')
            )
        );
    }

    public function testGetPropertyDocblockTypeHintDoesNotCrashWhenUnionType(): void
    {
        $resolver = new DocBlockTypeResolver();
        self::assertSame(
            'string',
            $resolver->getPropertyDocblockTypeHint(
                new ReflectionProperty(ObjectWithPhpDocProperty::class, 'firstname')
            )
        );
        self::assertSame(
            'string',
            $resolver->getPropertyDocblockTypeHint(
                new ReflectionProperty(ObjectWithPhpDocProperty::class, 'lastname')
            )
        );
    }
}
