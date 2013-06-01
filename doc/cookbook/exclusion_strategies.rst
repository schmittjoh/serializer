Exclusion Strategies
====================

Introduction
------------
The serializer supports different exclusion strategies. Each strategy allows
you to define which properties of your objects should be serialized.

General Exclusion Strategies
----------------------------
If you would like to always expose, or exclude certain properties. Then, you can
do this with the annotations ``@ExclusionPolicy``, ``@Exclude``, and ``@Expose``.

The default exclusion policy is to exclude nothing. That is, all properties of the
object will be serialized. If you only want to expose a few of the properties,
then it is easier to change the exclusion policy, and only mark these few properties:

.. code-block :: php

    <?php

    use JMS\Serializer\Annotation\ExclusionPolicy;
    use JMS\Serializer\Annotation\Expose;

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

.. note ::

    A property that is excluded by ``@Exclude`` cannot be exposed anymore by any
    of the following strategies, but is always hidden.

Versioning Objects
------------------
JMSSerializerBundle comes by default with a very neat feature which allows
you to add versioning support to your objects, e.g. if you want to
expose them via an API that is consumed by a third-party:

.. code-block :: php

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

.. note ::

    ``@Until``, and ``@Since`` both accept a standardized PHP version number.

If you have annotated your objects like above, you can serializing different
versions like this::

    use JMS\Serializer\SerializationContext;

    $serializer->serialize(new VersionObject(), 'json', SerializationContext::create()->setVersion(1));


Creating Different Views of Your Objects
----------------------------------------
Another default exclusion strategy is to create different views of your objects.
Let's say you would like to serialize your object in a different view depending
whether it is displayed in a list view or in a details view.

You can achieve that by using the ``@Groups`` annotation on your properties.

.. code-block :: php

    use JMS\Serializer\Annotation\Groups;

    class BlogPost
    {
        /** @Groups({"list", "details"}) */
        private $id;

        /** @Groups({"list", "details"}) */
        private $title;

        /** @Groups({"list"}) */
        private $nbComments;

        /** @Groups({"details"}) */
        private $comments;
    }

You can then tell the serializer which groups to serialize in your controller::

    use JMS\Serializer\SerializationContext;

    $serializer->serialize(new BlogPost(), 'json', SerializationContext::create()->setGroups(array('list')));

Limiting serialization depth of some properties
-----------------------------------------------
You can limit the depth of what will be serialized in a property with the
``@MaxDepth`` annotation.
This exclusion strategy is a bit different from the others, because it will
affect the serialized content of others classes than the one you apply the
annotation to.

.. code-block :: php

    use JMS\Serializer\Annotation\MaxDepth;

    class User
    {
        private $username;

        /** @MaxDepth(1) */
        private $friends;

        /** @MaxDepth(2) */
        private $posts;
    }

    class Post
    {
        private $title;

        private $author;
    }

In this example, serializing a user, because the max depth of the ``$friends``
property is 1, the user friends would be serialized, but not their friends;
and because the the max depth of the ``$posts`` property is 2, the posts would
be serialized, and their author would also be serialized.

You need to tell the serializer to take into account MaxDepth checks::

    use JMS\Serializer\SerializationContext;

    $serializer->serialize($data, 'json', SerializationContext::create()->enableMaxDepthChecks());
