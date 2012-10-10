Event System
============

The serializer dispatches different events during the serialization, and 
deserialization process which you can use to hook in and alter the default
behavior.

Register an Event Listener, or Subscriber
-----------------------------------------
A listener is a simple callable which receives an event object. You can
use the tags ``jms_serializer.event_listener``, or ``jms_serializer.event_subscriber``
in order to register it.

The semantics are mainly the same as registering a regular Symfony2 event listener 
except that you can to specify some additional attributes:

    - *format*: The format that you want to listen to; defaulting to all formats.
    - *type*: The type name that you want to listen to; defaulting to all types.
    - *direction*: The direction (serialization, or deserialization); defaulting to both.

Events
------

.. note ::

    Events are not dispatched by Symfony2's event dispatcher as such
    you cannot register listeners with the ``kernel.event_listener`` tag,
    or the @DI\Observe annotation. Please see above.

serializer.pre_serialize
~~~~~~~~~~~~~~~~~~~~~~~~
This is dispatched before a type is visited. You have access to the visitor,
data, and type. Listeners may modify the type that is being used for 
serialization.

**Event Object**: JMS\SerializerBundle\Serializer\EventDispatcher\PreSerializeEvent 

serializer.post_serialize
~~~~~~~~~~~~~~~~~~~~~~~~~
This is dispatched right before a type is left. You can for example use this
to add additional data to an object that you normally do not save inside
objects such as links.

**Event Object**: JMS\SerializerBundle\Serializer\EventDispatcher\Event 

serializer.post_deserialize
~~~~~~~~~~~~~~~~~~~~~~~~~~~
This is dispatched after a type is processed. You can use it to normalize 
submitted data if you require external services for example.

**Event Object**: JMS\SerializerBundle\Serializer\EventDispatcher\Event
