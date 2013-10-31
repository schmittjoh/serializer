This document details changes between individual versions.

For instructions on how to upgrade from one version to another, please see the dedicated UPGRADING document.

??? (???)
---------
- [BC Break] Passes DeserializationContext to ObjectConstructor instances as additional argument
- adds a handler for Propel related classes

0.13 (2013-07-29)
-----------------
- adds ability to serialize object graphs up to a certain depth (``@MaxDepth``)
- adds serializer.pre_deserialize event

0.12 (2013-03-28)
-----------------
- adds, and exposes SerializationContext/DeserializationContext
- adds built-in support for deserialization of polymorphic objects when they have a common base class
- adds a disjunct exclusion strategy
- allows @Type annotation to be used in combination with @VirtualProperty

0.11 (2013-01-29)
-----------------
Initial Release
