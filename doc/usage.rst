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

Format constants
----------------

If you're respecting "clean code" guidelines, you are able to use constants instead of string format names:

.. code-block :: php

    <?php

        $serializer = JMS\Serializer\SerializerBuilder::create()->build();
        $serializer->serialize($object, JMS\Serializer\SerializerInterface::FORMAT_JSON);
        $serializer->serialize($object, JMS\Serializer\SerializerInterface::FORMAT_XML);
