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

use Metadata\Driver\FileLocator;
use JMS\SerializerBundle\Metadata\PropertyMetadata;
use JMS\SerializerBundle\Metadata\Driver\YamlDriver;

class YamlDriverTest extends BaseDriverTest
{
    public function testBlogPostExcludeAllStrategy()
    {
        $m = $this->getDriver('exclude_all')->loadMetadataForClass(new \ReflectionClass('JMS\SerializerBundle\Tests\Fixtures\BlogPost'));

        $this->assertArrayHasKey('title', $m->propertyMetadata);

        $excluded = array('createdAt', 'published', 'comments', 'author');
        foreach ($excluded as $key) {
            $this->assertArrayNotHasKey($key, $m->propertyMetadata);
        }
    }

    public function testBlogPostExcludeNoneStrategy()
    {
        $m = $this->getDriver('exclude_none')->loadMetadataForClass(new \ReflectionClass('JMS\SerializerBundle\Tests\Fixtures\BlogPost'));

        $this->assertArrayNotHasKey('title', $m->propertyMetadata);

        $excluded = array('createdAt', 'published', 'comments', 'author');
        foreach ($excluded as $key) {
            $this->assertArrayHasKey($key, $m->propertyMetadata);
        }
    }

    public function testBlogPostCaseInsensitive()
    {
        $m = $this->getDriver('case')->loadMetadataForClass(new \ReflectionClass('JMS\SerializerBundle\Tests\Fixtures\BlogPost'));

        $p = new PropertyMetadata($m->name, 'title');
        $p->type = 'string';
        $this->assertEquals($p, $m->propertyMetadata['title']);
    }

    protected function getDriver()
    {
        $append = '';
        if (func_num_args() == 1) {
            $append = '/'.func_get_arg(0);
        }

        return new YamlDriver(new FileLocator(array(
            'JMS\SerializerBundle\Tests\Fixtures' => __DIR__.'/yml'.$append,
        )));
    }
}