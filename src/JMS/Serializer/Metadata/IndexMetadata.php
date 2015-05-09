<?php

/*
 * Copyright 2015 Ivan Borzenkov <ivan.borzenkov@gmail.com>
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

namespace JMS\Serializer\Metadata;

/**
 * Index Metadata used for saving array index in Context metadataStack
 *
 * @author Ivan Borzenkov <ivan.borzenkov@gmail.com>
 */
class IndexMetadata implements \Serializable
{
    public $index;

    /**
     * IndexMetadata constructor.
     *
     * @param $index
     */
    public function __construct($index)
    {
        $this->index = $index;
    }

    public function getValue()
    {
        return $this->index;
    }

    public function serialize()
    {
        return serialize(array(
            $this->index,
        ));
    }

    public function unserialize($str)
    {
        list(
            $this->index,
            ) = unserialize($str);
    }

}
