<?php

namespace JMS\Serializer;

use JMS\Serializer\Accessor\AccessorStrategyInterface;
use JMS\Serializer\Accessor\DefaultAccessorStrategy;

abstract class AbstractVisitor implements VisitorInterface
{
    protected $namingStrategy;

    /**
     * @var AccessorStrategyInterface
     */
    protected $accessor;

    public function __construct($namingStrategy, AccessorStrategyInterface $accessorStrategy = null)
    {
        $this->namingStrategy = $namingStrategy;
        $this->accessor = $accessorStrategy ?: new DefaultAccessorStrategy();
    }

    /**
     * @deprecated Will be removed in 2.0
     * @return mixed
     */
    public function getNamingStrategy()
    {
        return $this->namingStrategy;
    }

    public function prepare($data)
    {
        return $data;
    }

    /**
     * @param array $typeArray
     */
    protected function getElementType($typeArray)
    {
        if (false === isset($typeArray['params'][0])) {
            return null;
        }

        if (isset($typeArray['params'][1]) && \is_array($typeArray['params'][1])) {
            return $typeArray['params'][1];
        } else {
            return $typeArray['params'][0];
        }
    }
}
