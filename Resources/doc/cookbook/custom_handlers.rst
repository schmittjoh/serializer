Custom De-/Serialization Handlers
=================================

Introduction
------------
This allows you to change the way of how a specifc type is being de-/serialized.

Any handler must implement either the ``SerializationHandlerInterface``, or
``DeserializationHandlerInterface``, or both. This bundle already comes with
some handlers which you find in the Serializer/Handler folder, and which you
can use as a starting point.

Custom handlers are normal services, and thus can have dependencies on any
other service available in the dependency injection container.

Configuration
-------------
After you have written your handler, you can write a service definition. Such
as the following:

.. code-block :: xml

    <service id="acme_foo.serializer.my_handler"
             class="Acme\FooBundle\Serializer\MyHandler"
             public="false"
             />
             
The Handler Factory
-------------------
What is left to do is to publish our new handler to this bundle. So it gets
picked up, and wired correctly. In order to do this, this bundle uses a 
configuration system similar to that of the SecurityBundle. Each handler needs 
a corresponding definition factory:

.. code-block :: php

    <?php
    
    namespace Acme\FooBundle\DependencyInjection\Factory;
    
    use JMS\SerializerBundle\DependencyInjection\HandlerFactoryInterface;
    
    class MyHandlerFactory implements HandlerFactoryInterface
    {
        public function getConfigKey()
        {
            return 'acme_foo_my';
        }
        
        public function getType(array $config)
        {
            return self::TYPE_SERIALIZATION | self::TYPE_DESERIALIZATION;
        }
        
        public function addConfiguration(ArrayNodeDefinition $builder)
        {
            $builder
                ->children()
                    ->scalarNode('foo')->end()
                    ->scalarNode('bar')->end()
                ->end()
            ;
        }
        
        public function getHandlerId(ContainerBuilder $container, array $config)
        {
            return 'acme_foo.serializer.my_handler';
        }
    }
    
This factory is responsible for setting up the configuration for your handler
in the ``addConfiguration`` method, and then process that configuration in the
``getHandlerId`` method. 

The last thing left to do, is to add this factory to this bundle. This is
done by adding a ``configureSerializerExtension`` to your bundle class:

.. code-block :: php

    <?php
    
    namespace Acme\FooBundle;
    
    use Acme\FooBundle\DependencyInjection\Factory\FooFactory;
    use JMS\SerializerBundle\DependencyInjection\JMSSerializerExtension;
    use Symfony\Component\HttpKernel\Bundle\Bundle;
    
    class AcmeFooBundle extends Bundle
    {
        public function configureSerializerExtension(JMSSerializerExtension $ext)
        {
            $ext->addHandlerFactory(new FooFactory());
        }
    }

Enabling Your Handler
---------------------

TODO: Add example config

.. tip ::
    
    The order in which the handlers are listed in the "handlers" section defines
    in which they are called while processing. 
