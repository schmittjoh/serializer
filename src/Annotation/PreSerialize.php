<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * This annotation can be declared on methods which should be called
 * before the Serialization process.
 *
 * These methods do not need to be public, and should do any clean-up, or
 * preparation of the object that is necessary.
 *
 * @Annotation
 * @Target("METHOD")
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final class PreSerialize
{
}
