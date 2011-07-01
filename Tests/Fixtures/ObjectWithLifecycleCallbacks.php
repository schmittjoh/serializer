<?php

namespace JMS\SerializerBundle\Tests\Fixtures;

use JMS\SerializerBundle\Annotation\Exclude;
use JMS\SerializerBundle\Annotation\PreSerialize;
use JMS\SerializerBundle\Annotation\PostSerialize;
use JMS\SerializerBundle\Annotation\PreDeserialize;
use JMS\SerializerBundle\Annotation\PostDeserialize;
use JMS\SerializerBundle\Annotation\Type;

class ObjectWithLifecycleCallbacks
{
    /**
     * @Exclude
     */
    private $firstname;

    /**
     * @Exclude
     */
    private $lastname;

    /**
     * @Type("string")
     */
    private $name;

    /**
     * @Exclude
     */
    public $preDeserializeCalled = false;

    public function __construct($firstname = 'Foo', $lastname = 'Bar')
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }

    /**
     * @PreSerialize
     */
    private function prepareForSerialization()
    {
        $this->name = $this->firstname.' '.$this->lastname;
    }

    /**
     * @PostSerialize
     */
    private function cleanUpAfterSerialization()
    {
        $this->name = null;
    }

    /**
     * @PostDeserialize
     */
    private function afterDeserialization()
    {
        list($this->firstname, $this->lastname) = explode(' ', $this->name);
    }

    /**
     * @PreDeserialize
     */
    private function beforeDeserialization()
    {
        $this->preDeserializeCalled = true;
    }
}