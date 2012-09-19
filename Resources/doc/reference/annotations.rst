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

@AccessType
~~~~~~~~~~~
This annotation can be defined on a property, or a class to specify in which way
the properties should be accessed. By default, the serializer will retrieve, or
set the value via reflection, but you may change this to use a public method instead:

.. code-block :: php

    <?php
    use JMS\SerializerBundle\Annotation\AccessType;

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
    use JMS\SerializerBundle\Annotation\Accessor;

    class User
    {
        private $id;

        /** @Accessor(getter="getTrimmedName") */
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
    use JMS\SerializerBundle\Annotation\AccessorOrder;

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

@Inline
~~~~~~~~
This annotation can be defined on a property to indicate that the data of the property
should be inlined.

**Note**: This only works for serialization, the serializer will not be able to deserialize
objects with this annotation. Also, AccessorOrder will be using the name of the property
to determine the order.

@ReadOnly
~~~~~~~~~
This annotation can be defined on a property to indicate that the data of the property
is read only and cannot be set during deserialization.

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

Examples:

.. code-block :: php

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

.. code-block :: php

    <?php

    use JMS\SerializerBundle\Annotation\XmlRoot;

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

    use JMS\SerializerBundle\Annotation\XmlAttribute;

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

.. code-block :: php

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

@XmlMap
~~~~~~~
Similar to @XmlList, but the keys of the array are meaningful.

@XmlKeyValuePairs
~~~~~~~~~~~~~~~~~
This allows you to use the keys of an array as xml tags.

.. note ::

    When a key is an invalid xml tag name (e.g. 1_foo) the tag name *entry* will be used instead of the key.
