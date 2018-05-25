<?php

namespace JMS\Serializer\Construction;

use Doctrine\Instantiator\Instantiator;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\VisitorInterface;

class UnserializeObjectConstructor implements ObjectConstructorInterface
{
    /** @var Instantiator */
    private $instantiator;

    public function construct(VisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context)
    {
        return $this->getInstantiator()->instantiate($metadata->name);
    }

    /**
     * @return Instantiator
     */
    private function getInstantiator()
    {
        if (null == $this->instantiator) {
            $this->instantiator = new Instantiator();
        }

        return $this->instantiator;
    }
}
