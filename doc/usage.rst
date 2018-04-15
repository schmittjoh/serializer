Usage
=====

Serializing Objects
-------------------
Most common usage is probably to serialize objects. This can be achieved
very easily:

.. configuration-block ::

    .. code-block :: php

        <?php

        $serializer = JMS\Serializer\SerializerBuilder::create()->build();
        $serializer->serialize($object, 'json');
        $serializer->serialize($object, 'xml');

    .. code-block :: jinja

        {{ object | serialize }} {# uses JSON #}
        {{ object | serialize('json') }}
        {{ object | serialize('xml') }}

Deserializing Objects
---------------------
You can also deserialize objects from their XML, or JSON representation. For
example, when accepting data via an API.

.. code-block :: php

    <?php

    $serializer = JMS\Serializer\SerializerBuilder::create()->build();
    $object = $serializer->deserialize($jsonData, 'MyNamespace\MyObject', 'json');

