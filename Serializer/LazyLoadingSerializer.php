<?php

namespace JMS\SerializerBundle\Serializer;

use Symfony\Component\Serializer\SerializerAwareInterface;

class LazyLoadingSerializer extends Serializer
{
    private $container;

    protected function getEncoder($format)
    {
        $encoder = parent::getEncoder($format);

        if (is_string($encoder)) {
            $encoder = $this->container->get($encoder);

            if ($encoder instanceof SerializerAwareInterface) {
                $encoder->setSerializer($this);
            }
        }

        return $encoder;
    }
}