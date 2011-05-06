<?php

namespace JMS\SerializerBundle\Serializer\Normalizer;

interface NormalizerInterface
{
    function normalize($data, $format = null);
    function denormalize($data, $type, $format = null);
    function supportsNormalization($data, $format = null);
    function supportsDenormalization($data, $type, $format = null);
}