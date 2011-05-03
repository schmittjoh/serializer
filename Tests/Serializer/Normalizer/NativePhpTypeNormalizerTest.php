<?php

namespace JMS\SerializerBundle\Tests\Serializer\Normalizer;

use JMS\SerializerBundle\Serializer\Normalizer\NativePhpTypeNormalizer;

class NativePhpTypeNormalizerTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalizeDenormalize()
    {
        $normalizer = new NativePhpTypeNormalizer();

        $datetime = new \DateTime('now', new \DateTimeZone('Europe/Paris'));

        $this->assertEquals($datetime, $normalizer->denormalize($normalizer->normalize($datetime), 'DateTime'));
    }
}