<?php

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\XmlAttribute;
use JMS\Serializer\Annotation\XmlList;

class IndexedCommentsList
{
    /** @XmlList(inline=true, entry="comment") */
    #[XmlList(entry: 'comment', inline: true)]
    private $comments = [];

    /** @XmlAttribute */
    #[XmlAttribute]
    private $count = 0;

    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;
        $this->count += 1;
    }
}
