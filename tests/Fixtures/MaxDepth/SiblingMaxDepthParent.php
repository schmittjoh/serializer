<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures\MaxDepth;

use JMS\Serializer\Annotation as Serializer;

class SiblingMaxDepthParent
{
    /**
     * @Serializer\MaxDepth(3)
     */
    #[Serializer\MaxDepth(depth: 3)]
    public SiblingMaxDepthChild $deep;

    /**
     * @Serializer\MaxDepth(1)
     */
    #[Serializer\MaxDepth(depth: 1)]
    public SiblingMaxDepthChild $shallow;

    public function __construct()
    {
        $this->deep = new SiblingMaxDepthChild('deep');
        $this->shallow = new SiblingMaxDepthChild('shallow');
    }
}
