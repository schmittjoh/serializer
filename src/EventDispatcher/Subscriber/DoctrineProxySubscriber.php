<?php

declare(strict_types=1);

namespace JMS\Serializer\EventDispatcher\Subscriber;

use Doctrine\Common\Persistence\Proxy as LegacyProxy;
use Doctrine\ODM\MongoDB\PersistentCollection as MongoDBPersistentCollection;
use Doctrine\ODM\PHPCR\PersistentCollection as PHPCRPersistentCollection;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Persistence\Proxy;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use ProxyManager\Proxy\LazyLoadingInterface;

final class DoctrineProxySubscriber implements EventSubscriberInterface
{
    /**
     * @var bool
     */
    private $skipVirtualTypeInit;

    /**
     * @var bool
     */
    private $initializeExcluded;

    public function __construct(bool $skipVirtualTypeInit = true, bool $initializeExcluded = false)
    {
        $this->skipVirtualTypeInit = $skipVirtualTypeInit;
        $this->initializeExcluded = $initializeExcluded;
    }

    public function onPreSerialize(PreSerializeEvent $event): void
    {
        $object = $event->getObject();
        $type = $event->getType();

        // If the set type name is not an actual class, but a faked type for which a custom handler exists or
        // object is not an instance of that class we do not modify it with this subscriber.
        $virtualType = !class_exists($type['name']) || !$object instanceof $type['name'];

        if (
            $object instanceof PersistentCollection
            || $object instanceof MongoDBPersistentCollection
            || $object instanceof PHPCRPersistentCollection
        ) {
            if (!$virtualType) {
                $event->setType('ArrayCollection');
            }

            return;
        }

        if (
            ($this->skipVirtualTypeInit && $virtualType) ||
            (!$object instanceof Proxy && !$object instanceof LazyLoadingInterface)
        ) {
            return;
        }

        // do not initialize the proxy if is going to be excluded by-class by some exclusion strategy
        if (false === $this->initializeExcluded && !$virtualType) {
            $context = $event->getContext();
            $exclusionStrategy = $context->getExclusionStrategy();
            $metadata = $context->getMetadataFactory()->getMetadataForClass(get_parent_class($object));
            if (null !== $metadata && null !== $exclusionStrategy && $exclusionStrategy->shouldSkipClass($metadata, $context)) {
                return;
            }
        }

        if ($object instanceof LazyLoadingInterface) {
            $object->initializeProxy();
        } else {
            $object->__load();
        }

        if (!$virtualType) {
            $event->setType(get_parent_class($object), $type['params']);
        }
    }

    public function onPreSerializeTypedProxy(PreSerializeEvent $event, string $eventName, string $class, string $format, EventDispatcherInterface $dispatcher): void
    {
        $type = $event->getType();
        $object = $event->getObject();

        // is a virtual type or not an instance of? then there is no need to change the event name
        if (!class_exists($type['name']) || !$object instanceof $type['name']) {
            return;
        }

        if ($object instanceof Proxy) {
            $parentClassName = get_parent_class($object);

            // check if this is already a re-dispatch
            if (strtolower($class) !== strtolower($parentClassName)) {
                $event->stopPropagation();
                $newEvent = new PreSerializeEvent($event->getContext(), $object, ['name' => $parentClassName, 'params' => $type['params']]);
                $dispatcher->dispatch($eventName, $parentClassName, $format, $newEvent);

                // update the type in case some listener changed it
                $newType = $newEvent->getType();
                $event->setType($newType['name'], $newType['params']);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ['event' => 'serializer.pre_serialize', 'method' => 'onPreSerializeTypedProxy', 'interface' => Proxy::class],
            ['event' => 'serializer.pre_serialize', 'method' => 'onPreSerializeTypedProxy', 'interface' => LegacyProxy::class],
            ['event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize', 'interface' => PersistentCollection::class],
            ['event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize', 'interface' => MongoDBPersistentCollection::class],
            ['event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize', 'interface' => PHPCRPersistentCollection::class],
            ['event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize', 'interface' => Proxy::class],
            ['event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize', 'interface' => LegacyProxy::class],
            ['event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize', 'interface' => LazyLoadingInterface::class],
        ];
    }
}
