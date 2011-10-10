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
- can be configured via annotations, YAML, XML, or PHP


Installation
------------
First, checkout a copy of the code. Just add the following to the ``deps`` 
file of your Symfony Standard Distribution::

    [JMSSerializerBundle]
        git=git://github.com/schmittjoh/JMSSerializerBundle.git
        target=bundles/JMS/SerializerBundle

Then register the bundle with your kernel::

    // in AppKernel::registerBundles()
    $bundles = array(
        // ...
        new JMS\SerializerBundle\JMSSerializerBundle($this),
        // ...
    );

This bundle also requires the Metadata library (**you need the 1.1 version, not the 1.0
version** which ships with the Symfony Standard Edition.)::

    [metadata]
        git=http://github.com/schmittjoh/metadata.git
        version=1.1.0

Make sure that you also register the namespaces with the autoloader::

    // app/autoload.php
    $loader->registerNamespaces(array(
        // ...
        'JMS'              => __DIR__.'/../vendor/bundles',
        'Metadata'         => __DIR__.'/../vendor/metadata/src',
        // ...
    ));

Now use the ``vendors`` script to clone the newly added repositories 
into your project::

    php bin/vendors install

Configuration
-------------
Below is the default configuration, you don't need to change it unless it doesn't
suit your needs::

    jms_serializer:
        handlers:
            object_based: false
            datetime:
                format: Y-m-dTH:i:s
                default_timezone: UTC
            array_collection: true
            form_error: true
            constraint_violation: true

        property_naming:
            separator:  _
            lower_case: true

        metadata:
            cache: file
            debug: %kernel.debug%
            file_cache:
                dir: %kernel.cache_dir%/serializer

            # Using auto-detection, the mapping files for each bundle will be
            # expected in the Resources/config/serializer directory.
            #
            # Example:
            # class: My\FooBundle\Entity\User
            # expected path: @MyFooBundle/Resources/config/serializer/Entity.User.(yml|xml|php)
            auto_detection: true

            # if you don't want to use auto-detection, you can also define the
            # namespace prefix and the corresponding directory explicitly
            directories:
                any-name:
                    namespace_prefix: My\FooBundle
                    path: @MyFooBundle/Resources/config/serializer
                another-name:
                    namespace_prefix: My\BarBundle
                    path: @MyBarBundle/Resources/config/serializer

Note the order in which the handlers are listed in the "handlers" section defines
in which they are called while processing. See "extending.rst" for details for how
to define custom handlers, which then also need to be configured as shown here.

Usage
-----

De-/Serializing Objects
~~~~~~~~~~~~~~~~~~~~~~~

::

    $serializer = $container->get('serializer');
    $serializer->serialize(new MyObject(), 'json');
    $serializer->serialize(new MyObject(), 'xml');

The serializer supports JSON, and XML out-of-the-box, and can also handle
many custom XML features (see below).

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

If you have annotated your objects like above, you can serializing different
versions like this::

    <?php

    $serializer->setVersion('1.0');
    $serializer->serialize(new VersionObject(), 'json');


Defining which properties should be serialized
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The default exclusion policy is to exclude nothing, that is all properties of the
object will be serialized. If you only want to expose a few of the properties,
then it is easier to change the exclusion policy, and only mark these few properties::

    <?php

    use JMS\SerializerBundle\Annotation\ExclusionPolicy;
    use JMS\SerializerBundle\Annotation\Expose;

    /**
     * The following annotations tells the serializer to skip all properties which
     * have not marked with @Expose.
     *
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

Lifecycle Callbacks
~~~~~~~~~~~~~~~~~~~
If you need to run some custom logic during the serialization process, you can use
one of these lifecycle callbacks: @PreSerialize, @PostSerialize, or @PostDeserialize

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

@PreSerialize
~~~~~~~~~~~~~
This annotation can be defined on a method which is supposed to be called before
the serialization of the object starts.

@PostSerialize
~~~~~~~~~~~~~~
This annotation can be defined on a method which is then called directly after the
object has been serialized.

@PostDeserialize
~~~~~~~~~~~~~~~~
This annotation can be defined on a method which is supposed to be called after
the object has been deserialized.

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

@XmlRoot
~~~~~~~~
This allows you to specify the name of the top-level element.

::

    <?php

    use JMS\SerializerBundle\Annotation\XmlRoot;

    /** @XmlRoot("user") */
    class User
    {
        private $name = 'Johannes';
    }

Resulting XML::

    <user>
        <name><![CDATA[Johannes]]></name>
    </user>

@XmlAttribute
~~~~~~~~~~~~~
This allows you to mark properties which should be set as attributes,
and not as child elements.

::

    <?php

    use JMS\SerializerBundle\Annotation\XmlAttribute;

    class User
    {
        /** @XmlAttribute */
        private $id = 1;
        private $name = 'Johannes';
    }

Resulting XML::

    <result id="1">
        <name><![CDATA[Johannes]]></name>
    </result>
    
@XmlValue
~~~~~~~~~
This allows you to mark properties which should be set as the value of the
current element. Note that this has the limitation that any additional 
properties of that object must have the @XmlAttribute annotation.

::

    <?php
    
    use JMS\SerializerBundle\Annotation\XmlAttribute;
    use JMS\SerializerBundle\Annotation\XmlValue;
    use JMS\SerializerBundle\Annotation\XmlRoot;
    
    /** @XmlRoot("price") */
    class Price
    {
        /** @XmlAttribute */
        private $currency = 'EUR';
        
        /** @XmlValue */
        private $amount = 1.23;
    }
    
Resulting XML::

    <price currency="EUR">1.23</price>

@XmlList
~~~~~~~~
This allows you to define several properties of how arrays should be
serialized. This is very similar to @XmlMap, and should be used if the
keys of the array are not important.

::

    <?php

    use JMS\SerializerBundle\Annotation\XmlList;
    use JMS\SerializerBundle\Annotation\XmlRoot;

    /** @XmlRoot("post") */
    class Post
    {
        /**
         * @XmlList(inline = true, entry = "comment")
         */
        private $comments = array(
            new Comment('Foo'),
            new Comment('Bar'),
        );
    }

    class Comment
    {
        private $text;

        public function __construct($text)
        {
            $this->text = $text;
        }
    }

Resulting XML::

    <post>
        <comment>
            <text><![CDATA[Foo]]></text>
        </comment>
        <comment>
            <text><![CDATA[Bar]]></text>
        </comment>
    </post>

@XmlMap
~~~~~~~
Similar to @XmlList, but the keys of the array are meaningful.

XML Reference
-------------
::

    <!-- MyBundle\Resources\config\serializer\ClassName.xml -->
    <?xml version="1.0" encoding="UTF-8">
    <serializer>
        <class name="Fully\Qualified\ClassName" exclusion-policy="ALL" xml-root-name="foo-bar" exclude="true">
            <property name="some-property"
                      exclude="true"
                      expose="true"
                      type="string"
                      serialized-name="foo"
                      since-version="1.0"
                      until-version="1.1"
                      xml-attribute="true"
            >
                <!-- You can also specify the type as element which is necessary if
                     your type contains "<" or ">" characters. -->
                <type><![CDATA[]]></type>
                <xml-list inline="true" entry-name="foobar" />
                <xml-map inline="true" key-attribute-name="foo" entry-name="bar" />
            </property>
            <callback-method name="foo" type="pre-serialize" />
            <callback-method name="bar" type="post-serialize" />
            <callback-method name="baz" type="post-deserialize" />
        </class>
    </serializer>

YAML Reference
--------------
::

    # MyBundle\Resources\config\serializer\ClassName.yml
    Fully\Qualified\ClassName:
        exclusion_policy: ALL
        xml_root_name: foobar
        exclude: true
        properties:
            some-property:
                exclude: true
                expose: true
                type: string
                serialized_name: foo
                since_version: 1.0
                until_version: 1.1
                xml_attribute: true
                xml_list:
                    inline: true
                    entry_name: foo
                xml_map:
                    inline: true
                    key_attribute_name: foo
                    entry_name: bar
        callback_methods:
            pre_serialize: [foo, bar]
            post_serialize: [foo, bar]
            post_deserialize: [foo, bar]



