<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Metadata\Driver;

use JMS\Serializer\Metadata\Driver\DocBlockTypeResolver;
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
}
