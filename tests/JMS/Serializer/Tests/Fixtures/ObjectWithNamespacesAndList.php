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

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlNamespace;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlMap;
/**
 * @XmlRoot("ObjectWithNamespacesAndList", namespace="http://example.com/namespace")
 * @XmlNamespace(uri="http://example.com/namespace")
 * @XmlNamespace(uri="http://example.com/namespace2", prefix="x")
 */
class ObjectWithNamespacesAndList
{
    /**
     * @Type("string")
     * @SerializedName("name")
     * @XmlElement(namespace="http://example.com/namespace")
     */
    public $name;
    /**
     * @Type("string")
     * @SerializedName("name")
     * @XmlElement(namespace="http://example.com/namespace2")
     */
    public $nameAlternativeB;

    /**
     * @Type("array<string>")
     * @SerializedName("phones")
     * @XmlElement(namespace="http://example.com/namespace2")
     * @XmlList(inline = false, entry = "phone", namespace="http://example.com/namespace2")
     */
    public $phones;
    /**
     * @Type("array<string,string>")
     * @SerializedName("addresses")
     * @XmlElement(namespace="http://example.com/namespace2")
     * @XmlMap(inline = false, entry = "address", keyAttribute = "id", namespace="http://example.com/namespace2")
     */
    public $addresses;

    /**
     * @Type("array<string>")
     * @SerializedName("phones")
     * @XmlList(inline = true, entry = "phone", namespace="http://example.com/namespace")
     */
    public $phonesAlternativeB;
    /**
     * @Type("array<string,string>")
     * @SerializedName("addresses")
     * @XmlMap(inline = true, entry = "address", keyAttribute = "id", namespace="http://example.com/namespace")
     */
    public $addressesAlternativeB;

    /**
     * @Type("array<string>")
     * @SerializedName("phones")
     * @XmlList(inline = true, entry = "phone",  namespace="http://example.com/namespace2")
     */
    public $phonesAlternativeC;
    /**
     * @Type("array<string,string>")
     * @SerializedName("addresses")
     * @XmlMap(inline = true, entry = "address", keyAttribute = "id", namespace="http://example.com/namespace2")
     */
    public $addressesAlternativeC;

    /**
     * @Type("array<string>")
     * @SerializedName("phones")
     * @XmlList(inline = false, entry = "phone")
     */
    public $phonesAlternativeD;
    /**
     * @Type("array<string,string>")
     * @SerializedName("addresses")
     * @XmlMap(inline = false, entry = "address", keyAttribute = "id")
     */
    public $addressesAlternativeD;

}

