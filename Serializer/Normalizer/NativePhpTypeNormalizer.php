<?php

namespace JMS\SerializerBundle\Serializer\Normalizer;

use JMS\SerializerBundle\Exception\UnsupportedException;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class NativePhpTypeNormalizer extends SerializerAwareNormalizer
{
    private $dateTimeFormat;

    public function __construct($dateTimeFormat = \DateTime::ISO8601)
    {
        $this->dateTimeFormat = $dateTimeFormat;
    }

    public function normalize($data, $format = null)
    {
        if (null === $data || is_scalar($data)) {
            return $data;
        } else if (is_array($data) || $data instanceof \Traversable) {
            $normalized = array();
            foreach ($data as $k => $v) {
                $normalized[$k] = $this->serializer->normalize($v);
            }

            return $normalized;
        } else if ($data instanceof \DateTime) {
            return array(
                'time' => $data->format($this->dateTimeFormat),
                'timezone' => $data->getTimezone()->getName(),
            );
        }

        throw new UnsupportedException(sprintf('"%s" is not supported.', gettype($data)));
    }

    public function supportsNormalization($data, $format = null)
    {
        return null === $data
               || is_scalar($data)
               || is_array($data)
               || $data instanceof \DateTime
               || $data instanceof \Traversable;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return 'boolean' === $type
               || 'integer' === $type
               || 'string' === $type
               || 'DateTime' === $type
               || 0 === strpos($type, 'array');
    }

    public function denormalize($data, $type, $format = null)
    {
        if ('boolean' === $type) {
            return !!$data;
        } else if ('integer' === $type) {
            return (integer) $data;
        } else if ('string' === $type) {
            return (string) $data;
        } else if ('DateTime' === $type) {
            if (!is_array($data) && isset($data['time'], $data['timezone'])) {
                throw new \RuntimeException('Invalid input data for type "DateTime".');
            }

            $date = new \DateTime($data['time']);
            $date->setTimezone(new \DateTimeZone($data['timezone']));

            return $date;
        } else if (0 === strpos($type, 'array')) {
            if (!is_array($data)) {
                throw new \RuntimeException('Invalid input data for type "array".');
            }

            // unspecified array
            if ('array' === $type) {
                return $data;
            }

            // list
            if (false === $pos = strpos($type, ',')) {
                $listType = trim(substr($type, 6, -1));
                $denormalized = array();
                foreach ($data as $v) {
                    $denormalized[] = $this->serializer->denormalize($v, $listType, $format);
                }

                return $denormalized;
            }

            // map
            $keyType = trim(substr($type, 6, $pos - 6));
            $valueType = trim(substr($type, $pos, -1));
            $denormalized = array();
            foreach ($data as $k => $v) {
                $denormalized[$this->serializer->denormalize($k, $keyType, $format)]
                    = $this->serializer->denormalize($v, $valueType, $format);
            }

            return $denormalized;
        }

        throw new UnsupportedException(sprintf('Unsupported type "%s".', $type));
    }
}