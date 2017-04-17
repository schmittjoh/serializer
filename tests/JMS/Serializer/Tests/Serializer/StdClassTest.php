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

namespace JMS\Serializer\Tests\Serializer;

use JMS\Serializer\Handler\StdClassHandler;

class StdClassTest extends \PHPUnit_Framework_TestCase
{
    public function testFormat()
    {
        $dtf = new StdClassHandler();

        $emptyObject = $dtf->format(new \stdClass);

        $this->assertEquals($emptyObject, '{}');

        $object = new \stdClass;
        $object->prop1 = 'val1';
        $object->prop2 = null;

        $objectString = $dtf->format($object);

        $this->assertEquals($objectString, '{"prop1":"val1","prop2":null}');
    }
}
