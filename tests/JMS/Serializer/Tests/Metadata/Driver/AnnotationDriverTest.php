<?php

/*
 * Copyright 2013 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\Serializer\Tests\Metadata\Driver;

use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\Metadata\Driver\AnnotationDriver;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;

class AnnotationDriverTest extends BaseDriverTest
{
    protected function getDriver()
    {
        return new AnnotationDriver(new AnnotationReader());
    }

    public function testVirtualPropertyHasPriority()
    {
        $m = $this->getDriver()->loadMetadataForClass(new \ReflectionClass('JMS\Serializer\Tests\Fixtures\VitualPropertyIsPriority'));

        $this->assertNotNull($m);

        $p = new VirtualPropertyMetadata($m->name, 'id');
        $p->groups = array('testing_group');
        $p->getter = 'getId';
        $p->serializedName = 'id';
        $this->assertEquals($p, $m->propertyMetadata['id']);
    }
}
