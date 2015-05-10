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

namespace JMS\Serializer\Exception;
use JMS\Serializer\Context;

/**
 * DeserializeException for the Serializer.
 *
 * @author Ivan Borzenkov <ivan.borzenkov@gmail.com>
 */
class DeserializeException extends RuntimeException
{
    private $context;
    private $type;
    private $data;

    /**
     * @param array   $type
     * @param mixed   $data
     * @param Context $context
     */
    public function __construct($type, $data, Context $context)
    {
        $this->context = $context;
        $this->type = $type;
        $this->data = $data;
        parent::__construct('', 0, null);
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

}
