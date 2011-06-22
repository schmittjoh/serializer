<?php

namespace JMS\SerializerBundle\Annotation;

/**
 * This annotation can be defined on methods which are called after the
 * deserialization of the object is complete.
 *
 * These methods do not necessarily have to be public.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class PostDeserialize
{
}