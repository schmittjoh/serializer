<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
    /**
     * {@inheritDoc}
     */
    public function normalize($data, $format = null)
    {
        throw new UnsupportedException('This normalizer is only used for denormalization.');
    }

    /**
     * {@inheritDoc}
     */
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

    /**
     * {@inheritDoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return 0 === strpos($type, 'ArrayCollection<') && '>' === $type[strlen($type)-1];
    }
}