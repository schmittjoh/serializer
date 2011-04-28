========
Overview
========

This bundle allows you to easily serialize/unserialize objects. Main features
include:

- able to handle circular references, and object graphs of any depth without
  writing a single line of code
- serialize/unserialize objects using annotations to describe metadata
- supports versioning out of the box
- easily customizable as most logic is implemented using clearly defined
  interfaces

You need to have control over the objects that you want to serialize/unserialize.
This bundle does not work for objects provided by a third-party.

TODO:

- the unserialization process is not yet completely implemented (I currently 
  don't need it, but contributions are welcome)

Installation
------------
Checkout a copy of the code::

    git submodule add https://github.com/schmittjoh/SerializerExtraBundle.git src/JMS/SerializerExtraBundle
    
Then register the bundle with your kernel::

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new JMS\SerializerExtraBundle\SerializerExtraBundle(),
        // ...
    );

Configuration
-------------
Below is the default configuration, you don't need to change it unless it doesn't
suit your needs::

    jms_serializer_extra:
        naming_strategy:
            separator:  _
            lower_case: true

        encoders:
            xml:  true
            json: true

Usage
-----
The bundle configures a factory, and a default serializer for you that you can
use in your application code.

The default serializer is used if you do not care about versioning::

    $serializer = $container->get('serializer');

The serializer factory can be used if you want to display a specific version of
an object::

    $factory = $container->get('serializer_factory');
    $serializer = $factory->getSerializer('1.0.0');

Annotations
-----------

@ExclusionPolicy
~~~~~~~~~~~~~~~~
This annotation can be defined on a class to indicate the exclusion strategy
that should be used for the class.

::

    <?php
    /**
     * @ExclusionPolicy("NONE")
     */
    class MyObject
    {
    }

+----------+----------------------------------------------------------------+
| Policy   | Description                                                    |
+==========+================================================================+
| all      | all properties are excluded by default; only properties marked |
|          | with @Expose will be serialized/unserialized                   |
+----------+----------------------------------------------------------------+
| none     | no properties are excluded by default; all properties except   |
|          | those marked with @Exclude will be serialized/unserialized     |
+----------+----------------------------------------------------------------+

@Exclude
~~~~~~~~
This annotation can be defined on a property to indicate that the property should
not be serialized/unserialized. Works only in combination with NoneExclusionPolicy.

@Expose
~~~~~~~
This annotation can be defined on a property to indicate that the property should
be serialized/unserialized. Works only in combination with AllExclusionPolicy.

@SerializedName
~~~~~~~~~~~~~~~
This annotation can be defined on a property to define the serialized name for a
property. If this is not defined, the property will be translated from camel-case
to a lower-cased underscored name, e.g. camelCase -> camel_case.

::

    <?php
    class MyObject
    {
        /**
         * @SerializedName("some_other_name")
         */
        private $camelCase;
    }

@Since
~~~~~~
This annotation can be defined on a property to specify starting from which
version this property is available. If an earlier version is serialized, then
this property is excluded automatically.

@Until
~~~~~~
This annotation can be defined on a property to specify until which version this
property was available. If a later version is serialized, then this property is
excluded automatically.
