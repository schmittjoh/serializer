<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use Closure;
use ProxyManager\Proxy\LazyLoadingInterface;

class SimpleObjectLazyLoading extends SimpleObject implements LazyLoadingInterface
{
    private $isInitialized = false;

    private $initializer;

    private $baz = 'baz';

    public function __load()
    {
        if (!$this->isInitialized) {
            $this->camelCase = 'proxy-boo';
            $this->isInitialized = true;
        }
    }

    public function __isInitialized()
    {
        return $this->isInitialized;
    }

    /**
     * {@inheritDoc}
     */
    public function setProxyInitializer(?Closure $initializer = null)
    {
        $this->initializer = $initializer;
    }

    public function getProxyInitializer(): ?Closure
    {
        return $this->initializer;
    }

    public function initializeProxy(): bool
    {
        if (!$this->isInitialized) {
            $this->camelCase = 'proxy-boo';
            $this->isInitialized = true;

            return !$this->initializer || call_user_func($this->initializer);
        }

        return true;
    }

    public function isProxyInitialized(): bool
    {
        return $this->isInitialized;
    }
}
