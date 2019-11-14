<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use Closure;
use ProxyManager\Proxy\LazyLoadingInterface;

class SimpleObjectLazyLoading extends SimpleObject implements LazyLoadingInterface
{
    public $__isInitialized__ = false;

    private $initializer;

    private $baz = 'baz';

    public function __load()
    {
        if (!$this->__isInitialized__) {
            $this->camelCase = 'proxy-boo';
            $this->__isInitialized__ = true;
        }
    }

    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     */
    public function setProxyInitializer(?Closure $initializer = null)
    {
        $this->initializer = $initializer;
    }

    /**
     * {@inheritDoc}
     */
    public function getProxyInitializer(): ?Closure
    {
        return $this->initializer;
    }

    /**
     * {@inheritDoc}
     */
    public function initializeProxy(): bool
    {
        if (!$this->__isInitialized__) {
            $this->camelCase = 'proxy-boo';
            $this->__isInitialized__ = true;

            return !$this->initializer || call_user_func($this->initializer);
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isProxyInitialized(): bool
    {
        return $this->__isInitialized__;
    }
}
