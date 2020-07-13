Handlers
========

Introduction
------------
Handlers allow you to change the serialization, or deserialization process
for a single type/format combination.

Handlers are simple callback which receive three arguments: the visitor,
the data, and the type.

Simple Callables
----------------
You can register simple callables on the builder object::

    $builder
        ->configureHandlers(function(JMS\Serializer\Handler\HandlerRegistry $registry) {
            $registry->registerHandler('serialization', 'MyObject', 'json',
                function($visitor, MyObject $obj, array $type) {
                    return $obj->getName();
                }
            );
        })
    ;

.. note ::

        Be aware that when you call `configureHandlers` default handlers (like `DateHandler`)
        won't be added and you will have to call `addDefaultHandlers` on the Builder

Subscribing Handlers
--------------------
Subscribing handlers contain the configuration themselves which makes them easier to share with other users,
and easier to set-up in general::

    use JMS\Serializer\Handler\SubscribingHandlerInterface;
    use JMS\Serializer\GraphNavigator;
    use JMS\Serializer\JsonSerializationVisitor;
    use JMS\Serializer\JsonDeserializationVisitor;
    use JMS\Serializer\Context;

    class MyHandler implements SubscribingHandlerInterface
    {
        public static function getSubscribingMethods()
        {
            return [
                [
                    'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                    'format' => 'json',
                    'type' => 'DateTime',
                    'method' => 'serializeDateTimeToJson',
                ],
                [
                    'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                    'format' => 'json',
                    'type' => 'DateTime',
                    'method' => 'deserializeDateTimeToJson',
                ],
            ];
        }

        public function serializeDateTimeToJson(JsonSerializationVisitor $visitor, \DateTime $date, array $type, Context $context)
        {
            return $date->format($type['params'][0]);
        }

        public function deserializeDateTimeToJson(JsonDeserializationVisitor $visitor, $dateAsString, array $type, Context $context)
        {
            return new \DateTime($dateAsString);
        }
    }

Also, this type of handler is registered via the builder object::

    $builder
        ->configureHandlers(function(JMS\Serializer\Handler\HandlerRegistry $registry) {
            $registry->registerSubscribingHandler(new MyHandler());
        })
    ;

Skippable Subscribing Handlers
-------------------------------

In case you need to be able to fall back to the default deserialization behavior instead of using your custom
handler, you can simply throw a `SkipHandlerException` from you custom handler method to do so.
