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

This bundle works best when you have full control over the objects that you want
to serialize/unserialize as you can leverage the full power of annotations then.
If you want to serialize/deserialize objects provided by third parties, then you
need to write a custom normalizer for these objects.

Installation
------------
Checkout a copy of the code::

    git submodule add https://github.com/schmittjoh/SerializerBundle.git src/JMS/SerializerBundle

Then register the bundle with your kernel::

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new JMS\SerializerBundle\JMSSerializerBundle(),
        // ...
    );

This bundle also requires the Metadata library::

    git submodule add https://github.com/schmittjoh/metadata.git vendor/metadata

Make sure that you also register the namespaces with the autoloader::

    // app/autoload.php
    $loader->registerNamespaces(array(
        // ...
        'JMS'              => __DIR__.'/../vendor/bundles',
        'Metadata'         => __DIR__.'/../vendor/metadata/src',
        // ...
    ));    


Configuration
-------------
Below is the default configuration, you don't need to change it unless it doesn't
suit your needs::

    jms_serializer:
        normalization:
            date_format: Y-m-d\TH:i:sO
            naming:
                separator:  _
                lower_case: true
            doctrine_support: true
            
            # An array of version numbers: [1.0.0, 1.0.1, ...]
            versions: []

Usage
-----

Factory vs. Default Instance
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The bundle configures a factory, and a default serializer for you that you can
use in your application code.

The default serializer is used if you do not care about versioning::

    $serializer = $container->get('serializer');
    $serializer->serialize(new MyObject(), 'json');

The serializer factory can be used if you want to display a specific version of
an object::

    $factory = $container->get('serializer_factory');
    $serializer = $factory->getSerializer('1.0.0');
    $serializer->serialize(new MyVersionedObject(), 'json');

Versioning
~~~~~~~~~~

The bundle allows you to have different versions of your objects. This can be
achieved by using the @Since, and @Until annotation which both accept a 
standardized PHP version number.

::

    <?php
    
    class VersionedObject
    {
        /**
         * @Until("1.0.x")
         */
        private $name;
        
        /**
         * @Since("1.1")
         * @SerializedName("name")
         */
        private $name2;
    }

Changing the Exclusion Policy
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The default exclusion policy is to exclude nothing, that is all properties of the
object will be included in the normalized representation. If you only want to
expose a few of the properties, then it is easier to change the exclusion policy,
and only mark these few properties::

    <?php

    /**
     * @ExclusionPolicy("all")
     */
    class MyObject
    {
        private $foo;
        private $bar;

        /**
         * @Expose
         */
        private $name;
    }

In the above example, only the "name" property will be included in the normalized
representation.

Customizing the Serialization Process
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

There are several ways how you can customize the serialization process:

1. Using annotations (see below)
2. Implementing NormalizableInterface
3. Adding a Custom Normalizer

Wiring Custom Normalizers/Encoders
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

If you want to add custom normalizers, or encoders, you simply have to implement
either the ``JMS\SerializerBundle\Serializer\Normalizer\NormalizerInterface`` or
the ``JMS\SerializerBundle\Serializer\Encoder\EncoderInterface`` interface.

For normalizers, you can then use the following tag::

    <service id="my.custom.normalizer">
        <tag name="jms_serializer.normalizer" />
    </service>

For encoders, you also have to pass the format::

    <service id="my.custom.xml.encoder">
        <tag name="jms_serializer.encoder" format="xml" />
    </service>

Annotations
-----------

@ExclusionPolicy
~~~~~~~~~~~~~~~~
This annotation can be defined on a class to indicate the exclusion strategy
that should be used for the class.

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

@Since
~~~~~~
This annotation can be defined on a property to specify starting from which
version this property is available. If an earlier version is serialized, then
this property is excluded automatically. The version must be in a format that is
understood by PHP's ``version_compare`` function.

@Until
~~~~~~
This annotation can be defined on a property to specify until which version this
property was available. If a later version is serialized, then this property is
excluded automatically. The version must be in a format that is understood by 
PHP's ``version_compare`` function.

@Type
~~~~~
This annotation can be defined on a property to specify the type of that property.
This annotation must only be defined when you want to be able to deserialize an
object.

Available Types:

+---------------------------+--------------------------------------------------+
| Type                      | Description                                      |
+===========================+==================================================+
| boolean                   | Primitive boolean                                |
+---------------------------+--------------------------------------------------+
| integer                   | Primitive integer                                |
+---------------------------+--------------------------------------------------+
| double                    | Primitive double                                 |
+---------------------------+--------------------------------------------------+
| string                    | Primitive string                                 |
+---------------------------+--------------------------------------------------+
| array                     | An array with arbitrary keys, and values.        |
+---------------------------+--------------------------------------------------+
| array<T>                  | A list of type T (T can be any available type).  |
|                           | Examples:                                        |
|                           | array<string>, array<MyNamespace\MyObject>, etc. |
+---------------------------+--------------------------------------------------+
| array<K, V>               | A map of keys of type K to values of type V.     |
|                           | Examples: array<string, string>,                 |
|                           | array<string, MyNamespace\MyObject>, etc.        |
+---------------------------+--------------------------------------------------+
| DateTime                  | PHP's DateTime object                            |
+---------------------------+--------------------------------------------------+
| T                         | Where T is a fully qualified class name.         |
+---------------------------+--------------------------------------------------+
| ArrayCollection<T>        | Similar to array<T>, but will be deserialized    |
|                           | into Doctrine's ArrayCollection class.           |
+---------------------------+--------------------------------------------------+
| ArrayCollection<K, V>     | Similar to array<K, V>, but will be deserialized |
|                           | into Doctrine's ArrayCollection class.           |
+---------------------------+--------------------------------------------------+

Examples::

    <?php

    namespace MyNamespace;
    
    use JMS\SerializerBundle\Annotation\Type;

    class BlogPost
    {
        /**
         * @Type("ArrayCollection<MyNamespace\Comment>")
         */
        private $comments;

        /**
         * @Type("string")
         */
        private $title;

        /**
         * @Type("MyNamespace\Author")
         */
        private $author;

        /**
         * @Type("DateTime")
         */
        private $createdAt;

        /**
         * @Type("boolean")
         */
        private $published;

        /**
         * @Type("array<string, string>")
         */
        private $keyValueStore;
    }