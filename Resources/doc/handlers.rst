Handlers
========

Introduction
------------
Handlers allow you to change the serialization, or deserialization process
for a single type/format combination.

Handlers are simple callback which receive three arguments: the visitor,
the data, and the type. 

Configuration
-------------
You can register any service as a handler by adding either the ``jms_serializer.handler``,
or the ``jms_serializer.subscribing_handler``.

Using ``jms_serializer.subscribing_handler``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Using this tag, the configuration is contained in the handler itself which makes it
easier to share with other users, and easier to set-up in general:

.. code-block :: php

    <?php
    
    use JMS\SerializerBundle\Serializer\Handler\SubscribingHandlerInterface;
    use JMS\SerializerBundle\Serializer\GraphNavigator;
    use JMS\SerializerBundle\Serializer\JsonSerializationVisitor;
    
    class MyHandler implements SubscribingHandlerInterface
    {
        public static function getSubscribingMethods()
        {
            return array(
                array(
                    'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                    'format' => 'json',
                    'type' => 'DateTime',
                    'method' => 'serializeDateTimeToJson',
                ),
            );
        }
        
        public function serializeDateTimeToJson(JsonSerializationVisitor $visitor, \DateTime $date, array $type)
        {
            return $date->format($type['params'][0]);
        }
    }

.. code-block :: xml

    <service id="my_handler" class="MyHandler">
        <tag name="jms_serializer.subscribing_handler" />
    </service>

Using ``jms_serializer.handler``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
If you have a handler like above, you can also wire it using the ``jms_serializer.handler`` tag:

.. code-block :: xml

    <service id="my_handler" class="MyHandler" public="false">
        <tag name="jms_serializer.handler" type="DateTime" direction="serialization" format="json"
                    method="serializeDateTimeToJson" />
    </service>

.. tip ::

    The ``direction`` attribute is not required if you want to support both directions. Likewise can the
    ``method`` attribute be omitted, then a default using the scheme ``serializeTypeToFormat``,
    or ``deserializeTypeFromFormat`` will be used for serialization or deserialization
    respectively.
