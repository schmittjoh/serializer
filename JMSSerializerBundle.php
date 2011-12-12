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

namespace JMS\SerializerBundle;

use JMS\SerializerBundle\DependencyInjection\Factory\FormErrorFactory;
use JMS\SerializerBundle\DependencyInjection\Factory\DateTimeFactory;
use JMS\SerializerBundle\DependencyInjection\Factory\ConstraintViolationFactory;
use JMS\SerializerBundle\DependencyInjection\Factory\ArrayCollectionFactory;
use JMS\SerializerBundle\DependencyInjection\Factory\ObjectBasedFactory;
use JMS\SerializerBundle\DependencyInjection\Factory\DoctrineProxyFactory;
use JMS\SerializerBundle\DependencyInjection\JMSSerializerExtension;
use Symfony\Component\HttpKernel\KernelInterface;
use JMS\SerializerBundle\DependencyInjection\Compiler\SetVisitorsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class JMSSerializerBundle extends Bundle
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function getContainerExtension()
    {
        return new JMSSerializerExtension($this->kernel);
    }

    public function configureSerializerExtension(JMSSerializerExtension $ext)
    {
        $ext->addHandlerFactory(new ObjectBasedFactory());
        $ext->addHandlerFactory(new DoctrineProxyFactory());
        $ext->addHandlerFactory(new ArrayCollectionFactory());
        $ext->addHandlerFactory(new ConstraintViolationFactory());
        $ext->addHandlerFactory(new DateTimeFactory());
        $ext->addHandlerFactory(new FormErrorFactory());
    }

    public function build(ContainerBuilder $builder)
    {
        $builder->addCompilerPass(new SetVisitorsPass());
    }
}
