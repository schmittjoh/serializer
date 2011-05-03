<?php

namespace JMS\SerializerBundle\Serializer\Normalizer;

use JMS\SerializerBundle\Exception\UnsupportedException;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

/**
 * This normalizer is specifically designed for Doctrine's ArrayCollection.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ArrayCollectionNormalizer extends SerializerAwareNormalizer
{
    public function normalize($data, $format = null)
    {
        throw new UnsupportedException('This normalizer is only used for denormalization.');
    }

    public function denormalize($data, $type, $format = null)
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException('$data must be an array.');
        }

        if (!$this->supportsDenormalization($data, $type, $format)) {
            throw new UnsupportedException(sprintf('The type "%s" is not supported.', $type));
        }

        return new ArrayCollection($this->serializer->denormalize($data, 'array'.substr($type, 15), $format));
    }

    public function supportsNormalization($data, $format = null)
    {
        return false;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return 0 === strpos($type, 'ArrayCollection<') && '>' === $type[strlen($type)-1];
    }
}