Event System
============

The serializer dispatches different events during the serialization, and
deserialization process which you can use to hook in and alter the default
behavior.

Register an Event Listener, or Subscriber
-----------------------------------------
A listener is a simple callable which receives an event object.

The difference between both is similar to that of handlers. Listeners do not know to which they listen, but you
need to provide that information when they are registered. Subscribers on the hand, can be passed to the listener
and will tell the listener for which events they want to be called; this makes them easier to share, and re-use.

.. code-block :: php

    class MyEventSubscriber implements JMS\Serializer\EventDispatcher\EventSubscriberInterface
    {
        public function getSubscribingMethods()
        {
            return array(
                array('event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize'),
            );
        }

        public function onPreSerialize(JMS\Serializer\EventDispatcher\PreSerializeEvent $event)
        {
            // do something
        }
    }

    $builder
        ->configureListeners(function(JMS\Serializer\EventDispatcher\EventDispatcher $dispatcher) {
            $dispatcher->addListener('serializer.pre_serialize',
                function(JMS\Serializer\EventDispatcher\PreSerializeEvent $event) {
                    // do something
                }
            );

            $dispatcher->addSubscriber(new MyEventSubscriber());
        })
    ;

Events
------

serializer.pre_serialize
~~~~~~~~~~~~~~~~~~~~~~~~
This is dispatched before a type is visited. You have access to the visitor,
data, and type. Listeners may modify the type that is being used for
serialization.

**Event Object**: ``JMS\Serializer\EventDispatcher\PreSerializeEvent``

serializer.post_serialize
~~~~~~~~~~~~~~~~~~~~~~~~~
This is dispatched right before a type is left. You can for example use this
to add additional data to an object that you normally do not save inside
objects such as links.

**Event Object**: ``JMS\Serializer\EventDispatcher\Event``

serializer.post_deserialize
~~~~~~~~~~~~~~~~~~~~~~~~~~~
This is dispatched after a type is processed. You can use it to normalize
submitted data if you require external services for example.

**Event Object**: ``JMS\Serializer\EventDispatcher\Event``
