Object constructor
==================

Deserialize on existing objects
-------------------------------

By default, a brand new instance of target class is created during deserialization. To deserialize into an existing object, you need to perform the following steps.


1. Create new class which implements ObjectConstructorInterface

.. code-block:: php

    <?php declare(strict_types=1);

    namespace Acme\ObjectConstructor;

    use JMS\Serializer\Construction\ObjectConstructorInterface;
    use JMS\Serializer\DeserializationContext;
    use JMS\Serializer\Metadata\ClassMetadata;
    use JMS\Serializer\Visitor\DeserializationVisitorInterface;

    class ExistingObjectConstructor implements ObjectConstructorInterface
    {
        public const ATTRIBUTE = 'deserialization-constructor-target';

        private $fallbackConstructor;

        public function __construct(ObjectConstructorInterface $fallbackConstructor)
        {
            $this->fallbackConstructor = $fallbackConstructor;
        }

        public function construct(DeserializationVisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context): ?object
        {
            if ($context->hasAttribute(self::ATTRIBUTE)) {
                return $context->getAttribute(self::ATTRIBUTE);
            }

            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }
    }


2. Register ExistingObjectConstructor.

You should pass ExistingObjectConstructor to DeserializationGraphNavigatorFactory constructor.


3. Add special attribute to DeserializationContext

.. code-block:: php

    $context = DeserializationContext::create();
    $context->setAttribute('deserialization-constructor-target', $document);
    $serializer->deserialize($data, get_class($document), 'json');
