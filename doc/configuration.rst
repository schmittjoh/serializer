Configuration
=============

.. note ::

    If you are using Symfony2, this section is mostly irrelevant for you as the entire integration is provided by
    JMSSerializerBundle; please see `its documentation <http://jmsyst.com/bundles/JMSSerializerBundle>`_. If you are
    using another framework, there also might be a module, or other special integration. Please check packagist, or
    whatever registry usually holds such information for your framework.


If you are using the standalone library and you want to use annotations, the annotation registry must be initialized::

    Doctrine\Common\Annotations\AnnotationRegistry::registerLoader('class_exists');


Constructing a Serializer
-------------------------

This library provides a special builder object which makes constructing serializer instances a breeze in any PHP
project. In its shortest version, it's just a single line of code::

    $serializer = JMS\Serializer\SerializerBuilder::create()->build();

This serializer is fully functional, but you might want to tweak it a bit for example to configure a cache directory.

Configuring a Cache Directory
-----------------------------
The serializer collects several metadata about your objects from various sources such as YML, XML, or annotations. In
order to make this process as efficient as possible, it is encourage to let the serializer cache that information. For
that, you can configure a cache directory::

    $builder = new JMS\Serializer\SerializerBuilder();

    $serializer =
        JMS\Serializer\SerializerBuilder::create()
        ->setCacheDir($someWritableDir)
        ->setDebug($trueOrFalse)
        ->build();

As you can see, we also added a call to the ``setDebug`` method. In debug mode, the serializer will perform a bit more
filesystem checks to see whether the data that it has cached is still valid. These checks are useful during development
so that you do not need to manually clear cache folders, however in production they are just unnecessary overhead. The
debug setting allows you to make the behavior environment specific.

Adding Custom Handlers
----------------------
If you have created custom handlers, you can add them to the serializer easily::

    $serializer =
        JMS\Serializer\SerializerBuilder::create()
            ->addDefaultHandlers()
            ->configureHandlers(function(JMS\Serializer\Handler\HandlerRegistry $registry) {
                $registry->registerHandler(JMS\Serializer\GraphNavigatorInterface::DIRECTION_SERIALIZATION, 'MyObject', 'json',
                    function($visitor, MyObject $obj, array $type) {
                        return $obj->getName();
                    }
                );
            })
            ->build();

For more complex handlers, it is advisable to extract them to dedicated classes,
see :doc:`handlers documentation <handlers>`.

Configuring Metadata Locations
------------------------------
This library supports several metadata sources. By default, it uses Doctrine annotations, but you may also store
metadata in XML, or YML files. For the latter, it is necessary to configure a metadata directory where those files
are located::

    $serializer =
        JMS\Serializer\SerializerBuilder::create()
            ->addMetadataDir($someDir)
            ->build();

The serializer would expect the metadata files to be named like the fully qualified class names where all ``\`` are
replaced with ``.``. So, if you class would be named ``Vendor\Package\Foo``, the metadata file would need to be located
at ``$someDir/Vendor.Package.Foo.(xml|yml)``. If not found, ``$someDir/Vendor.Package.(xml|yml)`` will be tried, then ``$someDir/Vendor.Package.(xml|yml)`` and so on. For more information, see the :doc:`reference <reference>`.

Setting a default SerializationContext factory
----------------------------------------------
To avoid to pass an instance of SerializationContext
every time you call method ``serialize()`` (or ``toArray()``),
you can set a ``SerializationContextFactory`` to the Serializer.

Example using the SerializerBuilder::

    use JMS\Serializer\SerializationContext;

    $serializer = JMS\Serializer\SerializerBuilder::create()
        ->setSerializationContextFactory(function () {
            return SerializationContext::create()
                ->setSerializeNull(true)
            ;
        })
        ->build()
    ;

Then, calling ``$serializer->serialize($data, 'json');`` will generate
a serialization context from your callable and use it.

.. note ::

    You can also set a default DeserializationContextFactory with
    ``->setDeserializationContextFactory(function () { /* ... */ })``
    to be used with methods ``deserialize()`` and ``fromArray()``.
