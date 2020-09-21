<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use Doctrine\Persistence\Proxy;

class SimpleObjectProxy extends SimpleObject implements Proxy
{
    private $isInitialized = false;

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
}
