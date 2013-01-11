<?php

namespace JMS\Serializer\EventDispatcher\Subscriber;

use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\Proxy\Proxy as ORMProxy;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;

class DoctrineProxySubscriber implements EventSubscriberInterface
{
    public function onPreSerialize(PreSerializeEvent $event)
    {
        $object = $event->getObject();
        $type   = $event->getType();

        if ($object instanceof PersistentCollection) {
            $event->setType('ArrayCollection');

            return;
        }

        if ( ! $object instanceof Proxy && ! $object instanceof ORMProxy) {
            try {
                $class = new \ReflectionClass($type['name']);

                if ($class->isInterface()) {
                    $event->setType(get_class($object));
                }
            } catch (\ReflectionException $e) {}

            return;
        }

        $object->__load();
        $event->setType(get_parent_class($object));
    }

    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize'),
        );
    }
}
