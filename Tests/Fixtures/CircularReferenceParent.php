<?php

namespace JMS\SerializerExtraBundle\Tests\Fixtures;

use Doctrine\Common\Collections\ArrayCollection;

class CircularReferenceParent
{
    protected $collection = array();
    private $anotherCollection;

    public function __construct()
    {
        $this->collection[] = new CircularReferenceChild('child1', $this);
        $this->collection[] = new CircularReferenceChild('child2', $this);

        $this->anotherCollection = new ArrayCollection();
        $this->anotherCollection->add(new CircularReferenceChild('child1', $this));
        $this->anotherCollection->add(new CircularReferenceChild('child2', $this));
    }
}