Usage
=====

Serializing Objects
-------------------
Most common usage is probably to serialize objects. This can be achieved
very easily:

.. configuration-block ::

    .. code-block :: php
    
        <?php
        
        $serializer = $container->get('serializer');
        $serializer->serialize($object, 'json');
        $serializer->serialize($object, 'xml');
        $serializer->serialize($object, 'yml');
    
    .. code-block :: jinja
    
        {{ object | serialize }} {# uses JSON #}
        {{ object | serialize('json') }}
        {{ object | serialize('xml') }}
        {{ object | serialize('yml') }}
        
Deserializing Objects
---------------------
You can also deserialize objects from their XML, or JSON representation. For
example, when accepting data via an API.

.. code-block :: php

    <?php
    
    $serializer = $container->get('serializer');
    $object = $serializer->deserialize($jsonData, 'MyNamespace\MyObject', 'json');
    
More Resources
--------------

- :doc:`Customizing which data should be (de-)serialized </cookbook/exclusion_strategies>`
- :doc:`Adding custom serialization handlers </cookbook/custom_handlers>`
- :doc:`(De-)Serializing third-party objects </cookbook/metadata_for_third_party>`
- :doc:`Versioning Objects </cookbook/versioning_objects>`
