<?php

namespace JMS\SerializerBundle\Serializer\Normalizer;

use Symfony\Component\Serializer\SerializerInterface;

/**
 * This interface can be implemented by domain objects if they contain the
 * normalization logic themselves.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface NormalizableInterface
{
    /**
     * Normalizes the implementing object.
     *
     * @param SerializerInterface $serializer
     * @param string $format
     *
     * @return mixed
     */
    function normalize(SerializerInterface $serializer, $format = null);

    /**
     * Denormalizes the implementing object.
     *
     * @param SerializerInterface $serializer
     * @param mixed $data
     * @param string $format
     *
     * @return void
     */
    function denormalize(SerializerInterface $serializer, $data, $format = null);
}