<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

use JMS\SerializerBundle\Annotation\Accessor;
use JMS\SerializerBundle\Annotation\XmlMap;
use JMS\SerializerBundle\Annotation\XmlRoot;
use JMS\SerializerBundle\Annotation\XmlList;
use JMS\SerializerBundle\Annotation\XmlAttribute;

/** @XmlRoot("post") */
class IndexedCommentsBlogPost
{
    /**
     * @XmlMap(keyAttribute="author-name", inline=true, entry="comments")
     * @Accessor(getter="getCommentsIndexedByAuthor")
     */
    private $comments = array();

    public function __construct()
    {
        $author = new Author('Foo');
        $this->comments[] = new Comment($author, 'foo');
        $this->comments[] = new Comment($author, 'bar');
    }

    public function getCommentsIndexedByAuthor()
    {
        $indexedComments = array();
        foreach ($this->comments as $comment) {
            $authorName = $comment->getAuthor()->getName();

            if (!isset($indexedComments[$authorName])) {
                $indexedComments[$authorName] = new IndexedCommentsList();
            }

            $indexedComments[$authorName]->addComment($comment);
        }

        return $indexedComments;
    }
}

class IndexedCommentsList
{
    /** @XmlList(inline=true, entry="comment") */
    private $comments = array();

    /** @XmlAttribute */
    private $count = 0;

    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;
        $this->count += 1;
    }
}