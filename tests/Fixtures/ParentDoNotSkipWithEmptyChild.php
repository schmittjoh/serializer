<?php

namespace JMS\Serializer\Tests\Fixtures;

class ParentDoNotSkipWithEmptyChild
{
    private $c = 'c';

    private $d = 'd';

    /**
     * @var InlineChild
     */
    private $child;

    public function __construct($child = null)
    {
        $this->child = $child ?: new InlineChild();
    }
}
