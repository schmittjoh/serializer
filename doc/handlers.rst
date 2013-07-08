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

Subscribing Handlers
--------------------
Subscribing handlers contain the configuration themselves which makes them easier to share with other users,
and easier to set-up in general::

    use JMS\Serializer\Handler\SubscribingHandlerInterface;
    use JMS\Serializer\GraphNavigator;
    use JMS\Serializer\JsonSerializationVisitor;
    use JMS\Serializer\Context;

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

        public function serializeDateTimeToJson(JsonSerializationVisitor $visitor, \DateTime $date, array $type, Context $context)
        {
            return $date->format($type['params'][0]);
        }
    }

Also, this type of handler is registered via the builder object::

    $builder
        ->configureHandlers(function(JMS\Serializer\Handler\HandlerRegistry $registry) {
            $registry->registerSubscribingHandler(new MyHandler());
        })
    ;

