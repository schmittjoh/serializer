<?php

declare(strict_types=1);

namespace JMS\Serializer\Annotation;

/**
 * This annotation can be defined on methods which are called after the
 * deserialization of the object is complete.
 *
 * These methods do not necessarily have to be public.
 *
 * @Annotation
 * @Target("METHOD")
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final class PostDeserialize
{
}
