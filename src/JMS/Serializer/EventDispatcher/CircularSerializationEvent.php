<?php

namespace JMS\Serializer\EventDispatcher;

class CircularSerializationEvent extends ObjectEvent
{
    private $replacement = null;

    /**
     * @param mixed $replacement
     */
    public function setReplacement($replacement)
    {
        $this->replacement = $replacement;
    }

    /**
     * @return mixed
     */
    public function getReplacement()
    {
        return $this->replacement;
    }
}
