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
versions like this:

.. code-block :: php

    <?php

    $serializer->setVersion('1.0');
    $serializer->serialize(new VersionObject(), 'json');
