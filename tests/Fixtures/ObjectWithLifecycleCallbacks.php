<?php

declare(strict_types=1);

namespace JMS\Serializer\Tests\Fixtures;

use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\PostDeserialize;
use JMS\Serializer\Annotation\PostSerialize;
use JMS\Serializer\Annotation\PreSerialize;
use JMS\Serializer\Annotation\Type;

class ObjectWithLifecycleCallbacks
{
    /**
     * @Exclude
     */
    #[Exclude]
    private $firstname;

    /**
     * @Exclude
     */
    #[Exclude]
    private $lastname;

    /**
     * @Type("string")
     */
    #[Type(name: 'string')]
    private $name;

    public function __construct($firstname = 'Foo', $lastname = 'Bar')
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }

    /**
     * @PreSerialize
     */
    #[PreSerialize]
    private function prepareForSerialization()
    {
        $this->name = $this->firstname . ' ' . $this->lastname;
    }

    /**
     * @PostSerialize
     */
    #[PostSerialize]
    private function cleanUpAfterSerialization()
    {
        $this->name = null;
    }

    /**
     * @PostDeserialize
     */
    #[PostDeserialize]
    private function afterDeserialization()
    {
        [$this->firstname, $this->lastname] = explode(' ', $this->name);
        $this->name = null;
    }
}
