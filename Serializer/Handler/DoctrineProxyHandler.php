<?php

/*
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\SerializerBundle\Serializer\Handler;

use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\Proxy\Proxy as ORMProxy;
use JMS\SerializerBundle\Serializer\VisitorInterface;
use JMS\SerializerBundle\Serializer\Handler\SerializationHandlerInterface;

class DoctrineProxyHandler implements SerializationHandlerInterface
{
    public function serialize(VisitorInterface $visitor, $data, $type, &$handled)
    {
        if (($data instanceof Proxy || $data instanceof ORMProxy) && (!$data->__isInitialized__ || get_class($data) === $type)) {
            $handled = true;

            if (!$data->__isInitialized__) {
                $data->__load();
            }

            $navigator = $visitor->getNavigator();
            $navigator->detachObject($data);

            // pass the parent class not to load the metadata for the proxy class
            return $navigator->accept($data, get_parent_class($data), $visitor);
        }

        return null;
    }
}
