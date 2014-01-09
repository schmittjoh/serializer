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

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\XmlMap;
use JMS\Serializer\Annotation\XmlRoot;
use JMS\Serializer\Annotation\XmlNamespace;
use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlList;
use JMS\Serializer\Annotation\XmlElement;
use JMS\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use PhpCollection\Map;
use PhpCollection\Sequence;

/**
 * @XmlRoot("blog-post")
 * @XmlNamespace(uri="http://example.com/namespace")
 * @XmlNamespace(uri="http://schemas.google.com/g/2005", prefix="gd")
 * @XmlNamespace(uri="http://www.w3.org/2005/Atom", prefix="atom")
 * @XmlNamespace(uri="http://purl.org/dc/elements/1.1/", prefix="dc")
 */
class BlogPost
{
    /**
     * @Type("string")
     * @XmlElement(cdata=false)
     * @Groups({"comments","post"})
     */
    private $id = 'what_a_nice_id';

    /**
     * @Type("string")
     * @Groups({"comments","post"})
     * @XmlElement(namespace="http://purl.org/dc/elements/1.1/");
     */
    private $title;

    /**
     * @Type("DateTime")
     * @XmlAttribute
     */
    private $createdAt;

    /**
     * @Type("boolean")
     * @SerializedName("is_published")
     * @XmlAttribute
     * @Groups({"post"})
     */
    private $published;

    /**
     * @Type("string")
     * @XmlAttribute(namespace="http://schemas.google.com/g/2005")
     * @Groups({"post"})
     */
    private $etag;

    /**
     * @Type("ArrayCollection<JMS\Serializer\Tests\Fixtures\Comment>")
     * @XmlList(inline=true, entry="comment")
     * @Groups({"comments"})
     */
    private $comments;

    /**
     * @Type("PhpCollection\Sequence<JMS\Serializer\Tests\Fixtures\Comment>")
     * @XmlList(inline=true, entry="comment2")
     * @Groups({"comments"})
     */
    private $comments2;

    /**
     * @Type("PhpCollection\Map<string,string>")
     * @XmlMap(keyAttribute = "key")
     */
    private $metadata;

    /**
     * @Type("JMS\Serializer\Tests\Fixtures\Author")
     * @Groups({"post"})
     * @XmlElement(namespace="http://www.w3.org/2005/Atom")
     */
    private $author;

    /**
     * @Type("JMS\Serializer\Tests\Fixtures\Publisher")
     */
    private $publisher;

    public function __construct($title, Author $author, \DateTime $createdAt, Publisher $publisher)
    {
        $this->title = $title;
        $this->author = $author;
        $this->publisher = $publisher;
        $this->published = false;
        $this->comments = new ArrayCollection();
        $this->comments2 = new Sequence();
        $this->metadata = new Map();
        $this->metadata->set('foo', 'bar');
        $this->createdAt = $createdAt;
        $this->etag = sha1($this->createdAt->format(\DateTime::ISO8601));
    }

    public function setPublished()
    {
        $this->published = true;
    }

    public function getMetadata()
    {
        return $this->metadata;
    }

    public function addComment(Comment $comment)
    {
        $this->comments->add($comment);
        $this->comments2->add($comment);
    }
}
