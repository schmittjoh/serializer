Event System
============

The serializer dispatches different events during the serialization, and
deserialization process which you can use to hook in and alter the default
behavior.

Register an Event Listener, or Subscriber
-----------------------------------------
The difference between listeners, and subscribers is that listener do not know to which events they listen
while subscribers contain that information. Thus, subscribers are easier to share, and re-use. Listeners
on the other hand, can be simple callables and do not require a dedicated class.

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

**Event Object**: ``JMS\Serializer\EventDispatcher\ObjectEvent``

serializer.pre_deserialize
~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. versionadded : 0.12
    Event was added

This is dispatched before an object is deserialized. You can use this to
modify submitted data, or modify the type that is being used for deserialization.

**Event Object**: ``JMS\Serializer\EventDispatcher\PreDeserializeEvent``

serializer.post_deserialize
~~~~~~~~~~~~~~~~~~~~~~~~~~~
This is dispatched after a type is processed. You can use it to normalize
submitted data if you require external services for example, or also to
perform validation of the submitted data.

**Event Object**: ``JMS\Serializer\EventDispatcher\ObjectEvent``
