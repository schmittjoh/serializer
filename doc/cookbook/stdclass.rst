stdClass
========

The serializer offers support for serializing ``stdClass`` objects, however the use of
``stdClass`` objects is discouraged.

The current implementation serializes all the properties of a ``stdClass`` object in
the order they appear.

There are may know limitations wen dealing with ``stdClass`` objects,
more in detail, is not possible to:

- change serialization order of properties
- apply per-property exclusion policies
- specify any extra serialization information for properties that are part of the ``stdClass`` object, as serialization name, type, xml structure and so on
- deserialize data into ``stdClass`` objects
