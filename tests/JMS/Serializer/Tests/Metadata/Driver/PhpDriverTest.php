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

use Metadata\Driver\FileLocator;
use JMS\Serializer\Metadata\Driver\PhpDriver;

class PhpDriverTest extends BaseDriverTest
{
    protected function getDriver()
    {
        return new PhpDriver(new FileLocator(array(
            'JMS\Serializer\Tests\Fixtures' => __DIR__.'/php',
        )));
    }
}