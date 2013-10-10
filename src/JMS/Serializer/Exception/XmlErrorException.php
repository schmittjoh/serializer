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

namespace JMS\Serializer\Exception;

class XmlErrorException extends RuntimeException
{
    private $xmlError;

    public function __construct(\LibXMLError $error)
    {
        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $level = 'WARNING';
                break;

            case LIBXML_ERR_FATAL:
                $level = 'FATAL';
                break;

            case LIBXML_ERR_ERROR:
                $level = 'ERROR';
                break;

            default:
                $level = 'UNKNOWN';
        }

        parent::__construct(sprintf('[%s] %s in %s (line: %d, column: %d)', $level, $error->message, $error->file, $error->line, $error->column));

        $this->xmlError = $error;
    }

    public function getXmlError()
    {
        return $this->xmlError;
    }
}
