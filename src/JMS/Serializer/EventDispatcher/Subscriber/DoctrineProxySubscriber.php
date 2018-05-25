<?php

namespace JMS\Serializer\EventDispatcher\Subscriber;

use Doctrine\Common\Persistence\Proxy;
use Doctrine\ODM\MongoDB\PersistentCollection as MongoDBPersistentCollection;
use Doctrine\ODM\PHPCR\PersistentCollection as PHPCRPersistentCollection;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Proxy\Proxy as ORMProxy;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

class DoctrineProxySubscriber implements EventSubscriberInterface
{
    /**
     * @var bool
     */
    private $skipVirtualTypeInit = false;

    /**
     * @var bool
     */
    private $initializeExcluded = true;

    public function __construct($skipVirtualTypeInit = false, $initializeExcluded = true)
    {
        $this->skipVirtualTypeInit = (bool)$skipVirtualTypeInit;
        $this->initializeExcluded = (bool)$initializeExcluded;
    }

    public function onPreSerialize(PreSerializeEvent $event)
    {
        $object = $event->getObject();
        $type = $event->getType();

        // If the set type name is not an actual class, but a faked type for which a custom handler exists, we do not
        // modify it with this subscriber. Also, we forgo autoloading here as an instance of this type is already created,
        // so it must be loaded if its a real class.
        $virtualType = !class_exists($type['name'], false);

        if ($object instanceof PersistentCollection
            || $object instanceof MongoDBPersistentCollection
            || $object instanceof PHPCRPersistentCollection
        ) {
            if (!$virtualType) {
                $event->setType('ArrayCollection');
            }

            return;
        }

        if (($this->skipVirtualTypeInit && $virtualType) ||
            (!$object instanceof Proxy && !$object instanceof ORMProxy)
        ) {
            return;
        }

        // do not initialize the proxy if is going to be excluded by-class by some exclusion strategy
        if ($this->initializeExcluded === false && !$virtualType) {
            $context = $event->getContext();
            $exclusionStrategy = $context->getExclusionStrategy();
            if ($exclusionStrategy !== null && $exclusionStrategy->shouldSkipClass($context->getMetadataFactory()->getMetadataForClass(get_parent_class($object)), $context)) {
                return;
            }
        }

        $object->__load();

        if (!$virtualType) {
            $event->setType(get_parent_class($object), $type['params']);
        }
    }

    public function onPreSerializeTypedProxy(PreSerializeEvent $event, $eventName, $class, $format, EventDispatcherInterface $dispatcher)
    {
        $type = $event->getType();
        // is a virtual type? then there is no need to change the event name
        if (!class_exists($type['name'], false)) {
            return;
        }

        $object = $event->getObject();
        if ($object instanceof Proxy) {
            $parentClassName = get_parent_class($object);

            // check if this is already a re-dispatch
            if (strtolower($class) !== strtolower($parentClassName)) {
                $event->stopPropagation();
                $newEvent = new PreSerializeEvent($event->getContext(), $object, array('name' => $parentClassName, 'params' => $type['params']));
                $dispatcher->dispatch($eventName, $parentClassName, $format, $newEvent);

                // update the type in case some listener changed it
                $newType = $newEvent->getType();
                $event->setType($newType['name'], $newType['params']);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.pre_serialize', 'method' => 'onPreSerializeTypedProxy'),
            array('event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize'),
        );
    }
}
