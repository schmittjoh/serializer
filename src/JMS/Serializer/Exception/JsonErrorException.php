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

/**
 * JSON Error Exception
 *
 * Encapsulates an error which occured when serializing data into a string using
 * the json_encode() method or deserializing using the json_decode() method.
 *
 * @author Josiah <josiah@jjs.id.au>
 */
class JsonErrorException extends RuntimeException
{
    /**
     * Returns a new exception from the last json error
     * 
     * @return JsonErrorException
     */
    public static function fromLastError()
    {
        $code = json_last_error();
        $message = static::getLastErrorMessage();

        return new static($message, $code);
    }

    private static function getLastErrorMessage()
    {
        // PHP 5.5.0 has a new function 'json_last_error_msg' which provides the
        // error message from the last json error. This should be used in place
        // of this function where possible.
        if (function_exists('json_last_error_msg')) {
            return json_last_error_msg();
        }

        $code = json_last_error();

        switch ($code) {
            case JSON_ERROR_NONE:
                return null;
            case JSON_ERROR_DEPTH:
                return 'The maximum stack depth has been exceeded';
            case JSON_ERROR_STATE_MISMATCH:
                return 'Invalid or malformed JSON';
            case JSON_ERROR_CTRL_CHAR:
                return 'Control character error, possibly incorrectly encoded';
            case JSON_ERROR_SYNTAX:
                return 'Syntax error';
        }

        // Any PHP versions less than 5.3.3 won't have access to these constants
        if (version_compare(phpversion(), '5.3.3', '<')) {
            return null;
        }

        if ($code === JSON_ERROR_UTF8) {
            return 'Malformed UTF-8 characters, possibly incorrectly encoded';
        }

        // Other codes are unknown
        return "Unknown error ($code)";
    }
}
