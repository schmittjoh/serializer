<?php

/*
 * Copyright 2016 Johannes M. Schmitt <schmittjoh@gmail.com>
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

use JMS\Serializer\Handler\DateHandler;

class DateIntervalFormatTest extends \PHPUnit_Framework_TestCase
{
    public function testFormat()
    {
        $dtf = new DateHandler();

        $iso8601DateIntervalString = $dtf->format(new \DateInterval('P0D'));
        $this->assertEquals($iso8601DateIntervalString, 'P0DT0S');

        $iso8601DateIntervalString = $dtf->format(new \DateInterval('P0DT0S'));
        $this->assertEquals($iso8601DateIntervalString, 'P0DT0S');

        $iso8601DateIntervalString = $dtf->format(new \DateInterval('PT45M'));

        $this->assertEquals($iso8601DateIntervalString, 'PT45M');

        $iso8601DateIntervalString = $dtf->format(new \DateInterval('P2YT45M'));

        $this->assertEquals($iso8601DateIntervalString, 'P2YT45M');

        $iso8601DateIntervalString = $dtf->format(new \DateInterval('P2Y4DT6H8M16S'));

        $this->assertEquals($iso8601DateIntervalString, 'P2Y4DT6H8M16S');
    }
}
