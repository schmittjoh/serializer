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

namespace JMS\Serializer\Tests\Naming;

use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Metadata\PropertyMetadata;

class CamelCaseNamingStrategyTest extends \PHPUnit_Framework_TestCase
{
    private $testProperty;

    protected function setUp()
    {
        $this->testProperty = new PropertyMetadata(__CLASS__, 'testProperty');
    }

    public function testSeparator()
    {
        $strategy = new CamelCaseNamingStrategy('_');
        $this->assertEquals('test_property', $strategy->translateName($this->testProperty));

        $strategy = new CamelCaseNamingStrategy('-');
        $this->assertEquals('test-property', $strategy->translateName($this->testProperty));

        $strategy = new CamelCaseNamingStrategy('');
        $this->assertEquals('testproperty', $strategy->translateName($this->testProperty));
    }

    public function testLowerCase()
    {
        $strategy = new CamelCaseNamingStrategy('_', true);
        $this->assertEquals('test_property', $strategy->translateName($this->testProperty));

        $strategy = new CamelCaseNamingStrategy('', false);
        $this->assertEquals('TestProperty', $strategy->translateName($this->testProperty));
    }

    public function testFirstLetterLowerCase()
    {
        $strategy = new CamelCaseNamingStrategy('', false, false);
        $this->assertEquals('TestProperty', $strategy->translateName($this->testProperty));

        $strategy = new CamelCaseNamingStrategy('', false, true);
        $this->assertEquals('testProperty', $strategy->translateName($this->testProperty));
    }
}
