Serializing arrays and hashes
=============================

Introduction
------------
Serializing arrays and hashes (a concept that in PHP has not explicit boundaries)
can be challenging. The serializer offers via ``@Type`` annotation different options
to configure its behavior, but if we try to serialize directly an array
(not as a property of an object), we need to use context information to determine the
array "type"

Examples
--------

In case of a JSON serialization:

.. code-block :: php

    <?php

    // default (let the PHP's json_encode function decide)
    $serializer->serialize([1, 2]); //  [1, 2]
    $serializer->serialize(['a', 'b']); //  ['a', 'b']
    $serializer->serialize(['c' => 'd']); //  {"c" => "d"}

    // same as default (let the PHP's json_encode function decide)
    $serializer->serialize([1, 2], SerializationContext::create()->setInitialType('array')); //  [1, 2]
    $serializer->serialize([1 => 2], SerializationContext::create()->setInitialType('array')); //  {"1": 2}
    $serializer->serialize(['a', 'b'], SerializationContext::create()->setInitialType('array')); //  ['a', 'b']
    $serializer->serialize(['c' => 'd'], SerializationContext::create()->setInitialType('array')); //  {"c" => "d"}

    // typehint as strict array, keys will be always discarded
    $serializer->serialize([], SerializationContext::create()->setInitialType('array<integer>')); //  []
    $serializer->serialize([1, 2], SerializationContext::create()->setInitialType('array<integer>')); //  [1, 2]
    $serializer->serialize(['a', 'b'], SerializationContext::create()->setInitialType('array<integer>')); //  ['a', 'b']
    $serializer->serialize(['c' => 'd'], SerializationContext::create()->setInitialType('array<string>')); //  ["d"]

    // typehint as hash, keys will be always considered
    $serializer->serialize([], SerializationContext::create()->setInitialType('array<integer,integer>')); //  {}
    $serializer->serialize([1, 2], SerializationContext::create()->setInitialType('array<integer,integer>')); //  {"0" : 1, "1" : 2}
    $serializer->serialize(['a', 'b'], SerializationContext::create()->setInitialType('array<integer,integer>')); //  {"0" : "a", "1" : "b"}
    $serializer->serialize(['c' => 'd'], SerializationContext::create()->setInitialType('array<string,string>')); //  {"d" : "d"}


.. note ::

    This applies only for the JSON serialization.
