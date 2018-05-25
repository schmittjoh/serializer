<?php

namespace JMS\Serializer\EventDispatcher;

use JMS\Serializer\Context;

class Event
{
    /**
     * @var bool Whether no further event listeners should be triggered
     */
    private $propagationStopped = false;

    protected $type;
    private $context;

    public function __construct(Context $context, array $type)
    {
        $this->context = $context;
        $this->type = $type;
    }

    public function getVisitor()
    {
        return $this->context->getVisitor();
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns whether further event listeners should be triggered.
     *
     * @see Event::stopPropagation()
     *
     * @return bool Whether propagation was already stopped for this event
     */
    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }

    /**
     * Stops the propagation of the event to further event listeners.
     *
     * If multiple event listeners are connected to the same event, no
     * further event listener will be triggered once any trigger calls
     * stopPropagation().
     */
    public function stopPropagation()
    {
        $this->propagationStopped = true;
    }
}
