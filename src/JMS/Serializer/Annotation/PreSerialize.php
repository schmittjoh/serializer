<?php

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
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class PreSerialize
{
}
