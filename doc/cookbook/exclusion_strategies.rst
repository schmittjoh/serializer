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

You can achieve that by using the ``@Groups`` annotation on your properties. Any
property without an explicit ``@Groups`` annotation will be included in a
``Default`` group, which can be used when specifying groups in the serialization
context.

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

        private $createdAt;
    }

You can then tell the serializer which groups to serialize in your controller::

    use JMS\Serializer\SerializationContext;

    $serializer->serialize(new BlogPost(), 'json', SerializationContext::create()->setGroups(array('list')));

    //will output $id, $title and $nbComments.

    $serializer->serialize(new BlogPost(), 'json', SerializationContext::create()->setGroups(array('Default', 'list')));

    //will output $id, $title, $nbComments and $createdAt.

Overriding Groups of Deeper Branches of the Graph
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
In some cases you want to control more precisely what is serialized because you may have the same class at different
depths of the object graph.

For example if you have a User that has a manager and friends::

    use JMS\Serializer\Annotation\Groups;

    class User
    {
        private $name;

        /** @Groups({"manager_group"}) */
        private $manager;

        /** @Groups({"friends_group"}) */
        private $friends;

        public function __construct($name, User $manager = null, array $friends = null)
        {
            $this->name = $name;
            $this->manager = $manager;
            $this->friends = $friends;
        }
    }

And the following object graph::

    $john = new User(
        'John',
        new User(
            'John Manager',
            new User('The boss'),
            array(
                new User('John Manager friend 1'),
            )
        ),
        array(
            new User(
                'John friend 1',
                new User('John friend 1 manager')
            ),
            new User(
                'John friend 2',
                new User('John friend 2 manager')
            ),
        )
    );

You can override groups on specific paths::

    use JMS\Serializer\SerializationContext;

    $context = SerializationContext::create()->setGroups(array(
        'Default', // Serialize John's name
        'manager_group', // Serialize John's manager
        'friends_group', // Serialize John's friends

        'manager' => array( // Override the groups for the manager of John
            'Default', // Serialize John manager's name
            'friends_group', // Serialize John manager's friends. If you do not override the groups for the friends, it will default to Default.
        ),

        'friends' => array( // Override the groups for the friends of John
            'manager_group' // Serialize John friends' managers.

            'manager' => array( // Override the groups for the John friends' manager
                'Default', // This would be the default if you did not override the groups of the manager property.
            ),
        ),
    ));
    $serializer->serialize($john, 'json', $context);

This would result in the following json::

    {
        "name": "John",
        "manager": {
            "name": "John Manager",
            "friends": [
                {
                    "name": "John Manager friend 1"
                }
            ]
        },
        "friends": [
            {
                "manager": {
                    "name": "John friend 1 manager"
                },
            },
            {
                "manager": {
                    "name": "John friend 2 manager"
                },
            },
        ]
    }

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


Dynamic exclusion strategy
--------------------------

If the previous exclusion strategies are not enough, is possible to use the ``ExpressionLanguageExclusionStrategy``
that uses the `symfony expression language`_ to
allow a more sophisticated exclusion strategies using ``@Exclude(if="expression")`` and ``@Expose(if="expression")`` methods.
This also works on class level, but is only evaluated during ``serialze`` and does not have any effect during ``deserialze``.


.. code-block :: php

    <?php

    /**
     * @Exclude(if="true")
    */
    class MyObject
    {
        /**
         * @Exclude(if="true")
         */
        private $name;

       /**
         * @Expose(if="true")
         */
        private $name2;
    }

.. note ::

    ``true`` is just a generic expression, you can use any expression allowed by the Symfony Expression Language

To enable this feature you have to set the Expression Evaluator when initializing the serializer. 

.. code-block :: php

    <?php
    use JMS\Serializer\Expression\ExpressionEvaluator;
    use JMS\Serializer\Expression\SerializerBuilder;
    use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
    
    $serializer = SerializerBuilder::create()
        ->setExpressionEvaluator(new ExpressionEvaluator(new ExpressionLanguage()))
        ->build();

.. _symfony expression language: https://github.com/symfony/expression-language

By default the serializer exposes three variables (`object`, `context` and `property_metadata` for use in an expression. This enables you to create custom exclusion strategies similar to i.e. the `GroupExclusionStrategy`_. In the below example, `someMethod` would receive all three variables.

.. code-block :: php

    <?php

    class MyObject
    {
        /**
         * @Exclude(if="someMethod(object, context, property_metadata)")
         */
        private $name;

       /**
         * @Expose(if="someMethod(object, context, property_metadata)")
         */
        private $name2;
    }

.. _GroupExclusionStrategy: https://github.com/schmittjoh/serializer/blob/master/src/Exclusion/GroupsExclusionStrategy.php

Using dynamic excludes on class level is also handy when you need to filter out certain objects in a collection, for example based on user permissions.
The following example shows how to exclude `Account` objects when serializing the `Person` object, if the `Account` is either expired or the user does not have the permission to view the account by calling `is_granted` with the `Account` object.

.. code-block :: php

    <?php

    class Person
    {
        /**
         * @Type("array<Account>")
         */
        public $accounts;
    }

    /**
     * @Exclude(if="object.expired || !is_granted('view',object)")
     */
    class Account
    {
        /**
         * @Type("boolean")
         */
        public $expired;
    }
