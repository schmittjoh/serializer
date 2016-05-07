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

@Groups
~~~~~~~
This annotation can be defined on a property to specifiy to if the property
should be serialized when only serializing specific groups (see
:doc:`../cookbook/exclusion_strategies`).

@MaxDepth
~~~~~~~~~
This annotation can be defined on a property to limit the depth to which the
content will be serialized. It is very useful when a property will contain a
large object graph.

@AccessType
~~~~~~~~~~~
This annotation can be defined on a property, or a class to specify in which way
the properties should be accessed. By default, the serializer will retrieve, or
set the value via reflection, but you may change this to use a public method instead:

.. code-block :: php

    <?php
    use JMS\Serializer\Annotation\AccessType;

    /** @AccessType("public_method") */
    class User
    {
        private $name;

        public function getName()
        {
            return $this->name;
        }

        public function setName($name)
        {
            $this->name = trim($name);
        }
    }

@Accessor
~~~~~~~~~
This annotation can be defined on a property to specify which public method should
be called to retrieve, or set the value of the given property:

.. code-block :: php

    <?php
    use JMS\Serializer\Annotation\Accessor;

    class User
    {
        private $id;

        /** @Accessor(getter="getTrimmedName",setter="setName") */
        private $name;

        // ...
        public function getTrimmedName()
        {
            return trim($this->name);
        }

        public function setName($name)
        {
            $this->name = $name;
        }
    }

@AccessorOrder
~~~~~~~~~~~~~~
This annotation can be defined on a class to control the order of properties. By
default the order is undefined, but you may change it to either "alphabetical", or
"custom".

.. code-block :: php

    <?php
    use JMS\Serializer\Annotation\AccessorOrder;

    /**
     * @AccessorOrder("alphabetical")
     *
     * Resulting Property Order: id, name
     */
    class User
    {
        private $id;
        private $name;
    }

    /**
     * @AccessorOrder("custom", custom = {"name", "id"})
     *
     * Resulting Property Order: name, id
     */
    class User
    {
        private $id;
        private $name;
    }

    /**
     * @AccessorOrder("custom", custom = {"name", "someMethod" ,"id"})
     *
     * Resulting Property Order: name, mood, id
     */
    class User
    {
        private $id;
        private $name;

        /**
         * @Serializer\VirtualProperty
         * @Serializer\SerializedName("mood")
         *
         * @return string
         */
        public function getSomeMethod()
        {
            return 'happy';
        }
    }

@VirtualProperty
~~~~~~~~~~~~~~~~
This annotation can be defined on a method to indicate that the data returned by
the method should appear like a property of the object.

**Note**: This only works for serialization and is completely ignored during
deserialization.

@Inline
~~~~~~~
This annotation can be defined on a property to indicate that the data of the property
should be inlined.

**Note**: This only works for serialization, the serializer will not be able to deserialize
objects with this annotation. Also, AccessorOrder will be using the name of the property
to determine the order.

@ReadOnly
~~~~~~~~~
This annotation can be defined on a property to indicate that the data of the property
is read only and cannot be set during deserialization.

A property can be marked as non read only with ``@ReadOnly(false)`` annotation (useful when a class is marked as read only).

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

@HandlerCallback
~~~~~~~~~~~~~~~~
This annotation can be defined on a method if serialization/deserialization is handled
by the object iself.

.. code-block :: php

    <?php

    class Article
    {
        /**
         * @HandlerCallback("xml", direction = "serialization")
         */
        public function serializeToXml(XmlSerializationVisitor $visitor)
        {
            // custom logic here
        }
    }

@Discriminator
~~~~~~~~~~~~~~

.. versionadded : 0.12
    @Discriminator was added

This annotation allows deserialization of relations which are polymorphic, but
where a common base class exists. The ``@Discriminator`` annotation has to be applied
to the least super type::

    /**
     * @Discriminator(field = "type", map = {"car": "Car", "moped": "Moped"})
     */
    abstract class Vehicle { }
    class Car extends Vehicle { }
    class Moped extends Vehicle { }

@Type
~~~~~
This annotation can be defined on a property to specify the type of that property.
For deserialization, this annotation must be defined. For serialization, you may
define it in order to enhance the produced output; for example, you may want to
force a certain format to be used for DateTime types.

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
| DateTime                  | PHP's DateTime object (default format/timezone)  |
+---------------------------+--------------------------------------------------+
| DateTime<'format'>        | PHP's DateTime object (custom format/default     |
|                           | timezone)                                        |
+---------------------------+--------------------------------------------------+
| DateTime<'format', 'zone'>| PHP's DateTime object (custom format/timezone)   |
+---------------------------+--------------------------------------------------+
| T                         | Where T is a fully qualified class name.         |
+---------------------------+--------------------------------------------------+
| ArrayCollection<T>        | Similar to array<T>, but will be deserialized    |
|                           | into Doctrine's ArrayCollection class.           |
+---------------------------+--------------------------------------------------+
| ArrayCollection<K, V>     | Similar to array<K, V>, but will be deserialized |
|                           | into Doctrine's ArrayCollection class.           |
+---------------------------+--------------------------------------------------+

Examples:

.. code-block :: php

    <?php

    namespace MyNamespace;

    use JMS\Serializer\Annotation\Type;

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
         * @Type("DateTime<'Y-m-d'>")
         */
        private $updatedAt;

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

.. code-block :: php

    <?php

    use JMS\Serializer\Annotation\XmlRoot;

    /** @XmlRoot("user") */
    class User
    {
        private $name = 'Johannes';
    }

Resulting XML:

.. code-block :: xml

    <user>
        <name><![CDATA[Johannes]]></name>
    </user>

.. note ::

    @XmlRoot only applies to the root element, but is for example not taken into
    account for collections. You can define the entry name for collections using
    @XmlList, or @XmlMap.

@XmlAttribute
~~~~~~~~~~~~~
This allows you to mark properties which should be set as attributes,
and not as child elements.

.. code-block :: php

    <?php

    use JMS\Serializer\Annotation\XmlAttribute;

    class User
    {
        /** @XmlAttribute */
        private $id = 1;
        private $name = 'Johannes';
    }

Resulting XML:

.. code-block :: xml

    <result id="1">
        <name><![CDATA[Johannes]]></name>
    </result>

@XmlValue
~~~~~~~~~
This allows you to mark properties which should be set as the value of the
current element. Note that this has the limitation that any additional
properties of that object must have the @XmlAttribute annotation.
XMlValue also has property cdata. Which has the same meaning as the one in
XMLElement.

.. code-block :: php

    <?php

    use JMS\Serializer\Annotation\XmlAttribute;
    use JMS\Serializer\Annotation\XmlValue;
    use JMS\Serializer\Annotation\XmlRoot;

    /** @XmlRoot("price") */
    class Price
    {
        /** @XmlAttribute */
        private $currency = 'EUR';

        /** @XmlValue */
        private $amount = 1.23;
    }

Resulting XML:

.. code-block :: xml

    <price currency="EUR">1.23</price>

@XmlList
~~~~~~~~
This allows you to define several properties of how arrays should be
serialized. This is very similar to @XmlMap, and should be used if the
keys of the array are not important.

.. code-block :: php

    <?php

    use JMS\Serializer\Annotation\XmlList;
    use JMS\Serializer\Annotation\XmlRoot;

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

Resulting XML:

.. code-block :: xml

    <post>
        <comment>
            <text><![CDATA[Foo]]></text>
        </comment>
        <comment>
            <text><![CDATA[Bar]]></text>
        </comment>
    </post>

You can also specify the entry tag namespace using the ``namespace`` attribute (``@XmlList(inline = true, entry = "comment", namespace="http://www.example.com/ns")``). 

@XmlMap
~~~~~~~
Similar to @XmlList, but the keys of the array are meaningful.

@XmlKeyValuePairs
~~~~~~~~~~~~~~~~~
This allows you to use the keys of an array as xml tags.

.. note ::

    When a key is an invalid xml tag name (e.g. 1_foo) the tag name *entry* will be used instead of the key.

@XmlAttributeMap
~~~~~~~~~~~~~~~~

This is similar to the @XmlKeyValuePairs, but instead of creating child elements, it creates attributes.

.. code-block :: php

    <?php

    use JMS\Serializer\Annotation\XmlAttribute;

    class Input
    {
        /** @XmlAttributeMap */
        private $id = array(
            'name' => 'firstname',
            'value' => 'Adrien',
        );
    }

Resulting XML:

.. code-block :: xml

    <result name="firstname" value="Adrien"/>

@XmlElement
~~~~~~~~~~~
This annotation can be defined on a property to add additional xml serialization/deserialization properties.

.. code-block :: php

    <?php

    use JMS\Serializer\Annotation\XmlElement;

    /**
     * @XmlNamespace(uri="http://www.w3.org/2005/Atom", prefix="atom")
     */
    class User
    {
        /**
        * @XmlElement(cdata=false, namespace="http://www.w3.org/2005/Atom")
        */
        private $id = 'my_id';
    }

Resulting XML:

.. code-block :: xml

    <atom:id>my_id</atom:id>

@XmlNamespace
~~~~~~~~~~~~~
This annotation allows you to specify Xml namespace/s and prefix used.

.. code-block :: php

    <?php

    use JMS\Serializer\Annotation\XmlNamespace;

    /**
     * @XmlNamespace(uri="http://example.com/namespace")
     * @XmlNamespace(uri="http://www.w3.org/2005/Atom", prefix="atom")
     */
    class BlogPost
    {
        /**
         * @Type("JMS\Serializer\Tests\Fixtures\Author")
         * @Groups({"post"})
         * @XmlElement(namespace="http://www.w3.org/2005/Atom")
         */
         private $author;
    }

    class Author
    {
        /**
         * @Type("string")
         * @SerializedName("full_name")
         */
         private $name;
    }

Resulting XML:

.. code-block :: xml

    <?xml version="1.0" encoding="UTF-8"?>
    <blog-post xmlns="http://example.com/namespace" xmlns:atom="http://www.w3.org/2005/Atom">
        <atom:author>
            <full_name><![CDATA[Foo Bar]]></full_name>
        </atom:author>
    </blog>
