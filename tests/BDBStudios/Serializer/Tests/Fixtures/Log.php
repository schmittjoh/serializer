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

namespace BDBStudios\Serializer\Tests\Fixtures;

use BDBStudios\Serializer\Annotation\XmlRoot;
use BDBStudios\Serializer\Annotation\SerializedName;
use BDBStudios\Serializer\Annotation\XmlList;
use BDBStudios\Serializer\Annotation\XmlMap;
use BDBStudios\Serializer\Annotation\Type;

/** @XmlRoot("log") */
class Log
{
    /**
     * @SerializedName("author_list")
     * @XmlMap
     * @Type("AuthorList")
     */
    private $authors;

    /**
     * @XmlList(inline=true, entry = "comment")
     * @Type("array<BDBStudios\Serializer\Tests\Fixtures\Comment>")
     */
    private $comments;

    public function __construct()
    {
        $this->authors = new AuthorList();
        $this->authors->add(new Author('Johannes Schmitt'));
        $this->authors->add(new Author('John Doe'));

        $author = new Author('Foo Bar');
        $this->comments = array();
        $this->comments[] = new Comment($author, 'foo');
        $this->comments[] = new Comment($author, 'bar');
        $this->comments[] = new Comment($author, 'baz');
    }
}
