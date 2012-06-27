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

namespace JMS\SerializerBundle\Tests\Fixtures\Doctrine;

use JMS\SerializerBundle\Annotation\Type;
use JMS\SerializerBundle\Annotation\XmlValue;
use JMS\SerializerBundle\Annotation\XmlRoot;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @XmlRoot("price")
 */
class Price
{
    /** 
     * @ORM\Id @ORM\Column(type="integer") 
     */
    protected $id;

    /**
     * The guessed type for doctrine would be float, but here we're overriding
     * it to double.
     *
     * @ORM\Column(type="double")
     * @Type("double")
     * @XmlValue
     */
    private $price;

    function __construct($price)
    {
        $this->price = $price;
    }
}
