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

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlList;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\AccessType;

/**
 * @XmlRoot("person_collection")
 */
class PersonCollection
{
    /**
     * @Type("ArrayCollection<JMS\Serializer\Tests\Fixtures\Person>")
     * @XmlList(entry = "person", inline = true)
     */
    public $persons;

    /**
     * @Type("ArrayCollection<JMS\Serializer\Tests\Fixtures\Person>")
     * @XmlList(entry = "collection_aware_person", inline = true)
     * @AccessType("public_method")
     */
    private $collectionAwarePersons;

    /**
     * @Type("string")
     */
    public $location;

    public function __construct()
    {
        $this->persons = new ArrayCollection;
        $this->collectionAwarePersons = new ArrayCollection;
    }

    /**
     * @param ArrayCollection|Person[] $persons
     */
    public function setCollectionAwarePersons(ArrayCollection $persons)
    {
        $this->collectionAwarePersons = $persons;
        foreach ($this->collectionAwarePersons as $person) {
            $person->collection = $this;
        }
    }

    /**
     * @return ArrayCollection|Person[]
     */
    public function getCollectionAwarePersons()
    {
        return $this->collectionAwarePersons;
    }
}