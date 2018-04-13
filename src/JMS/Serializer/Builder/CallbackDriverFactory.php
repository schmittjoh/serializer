<?php

namespace JMS\Serializer\Builder;

use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\Metadata\ClassMetadataUpdaterInterface;
use Metadata\Driver\DriverInterface;

class CallbackDriverFactory implements DriverFactoryInterface
{
    private $callback;

    /**
     * @param callable $callable
     */
    public function __construct($callable)
    {
        $this->callback = $callable;
    }

    /**
     * {@inheritDoc}
     */
    public function createDriver(
        array $metadataDirs,
        Reader $reader,
        ClassMetadataUpdaterInterface $propertyUpdater = null
    )
    {
        $driver = \call_user_func($this->callback, $metadataDirs, $reader, $propertyUpdater);
        if (!$driver instanceof DriverInterface) {
            throw new \LogicException('The callback must return an instance of DriverInterface.');
        }

        return $driver;
    }
}
