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

namespace JMS\SerializerBundle\Tests\Metadata\Driver;

use JMS\SerializerBundle\Metadata\Driver\AnnotationDriver;
use JMS\SerializerBundle\Metadata\Driver\DoctrineTypeDriver;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\AnnotationReader;

class DoctrineDriverTest extends BaseDriverTest
{
    public function setUp()
    {
        $this->setFixtureNamespace("JMS\\SerializerBundle\\Tests\\Fixtures\\Doctrine");
    }

    protected function getEntityManager()
    {
        $config = new Configuration();
        $config->setProxyDir(sys_get_temp_dir() . '/JMSDoctrineTestProxies');
        $config->setProxyNamespace('JMS\Tests\Proxies');
        $config->setMetadataDriverImpl(
            new \Doctrine\ORM\Mapping\Driver\AnnotationDriver(new AnnotationReader(), __DIR__.'/../../Fixtures/Doctrine')
        );

        $conn = array(
            'driver'    => 'pdo_sqlite',
            'path'      => sys_get_temp_dir() . '/jms_test_database.sqlite',
        );

        return EntityManager::create($conn, $config);
    }

    protected function getDriver()
    {
        return new DoctrineTypeDriver(
            new AnnotationDriver(new AnnotationReader()),
            $this->getEntityManager()
        );
    }
}
