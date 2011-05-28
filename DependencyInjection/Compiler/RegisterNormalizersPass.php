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

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class RegisterNormalizersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $normalizers = array();
        foreach ($container->findTaggedServiceIds('jms_serializer.normalizer') as $id => $attributes) {
            $def = $container->findDefinition($id);
            $strict = ContainerInterface::SCOPE_PROTOTYPE !== $def->getScope();
            $priority = isset($attributes[0]['priority']) ? $attributes[0]['priority'] : 0;
            $normalizers[$priority][] = new Reference($id, ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $strict);
        }

        // sort by priority and flatten
        krsort($normalizers);
        $normalizers = call_user_func_array('array_merge', $normalizers);

        foreach (array_keys($container->findTaggedServiceIds('jms_serializer.serializer')) as $id) {
            $container
                ->getDefinition($id)
                ->addArgument($normalizers)
            ;
        }
    }
}
