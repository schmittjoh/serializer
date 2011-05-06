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

namespace JMS\SerializerBundle\DependencyInjection\Compiler;

use JMS\SerializerBundle\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class RegisterEncodersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $encoders = array();
        foreach ($container->findTaggedServiceIds('jms_serializer.encoder') as $id => $attributes) {
            if (!isset($attributes[0]['format'])) {
                throw new RuntimeException(sprintf('"format" attribute must be specified for service "%s" and tag "jms_serializer.encoder".', $id));
            }

            $encoders[$attributes[0]['format']] = $id;
        }

        foreach (array_keys($container->findTaggedServiceIds('jms_serializer.serializer')) as $id) {
            $container
                ->getDefinition($id)
                ->addArgument($encoders)
            ;
        }
    }
}