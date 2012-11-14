<?php

namespace JMS\SerializerBundle\Serializer\EventDispatcher\Subscriber;

use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\Proxy\Proxy as ORMProxy;
use JMS\SerializerBundle\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\SerializerBundle\Serializer\EventDispatcher\EventSubscriberInterface;

class DoctrineProxySubscriber implements EventSubscriberInterface
{
    public function onPreSerialize(PreSerializeEvent $event)
    {
        $object = $event->getObject();

        if ($object instanceof PersistentCollection) {
            $event->setType('ArrayCollection');

            return;
        }

        if ( ! $object instanceof Proxy && ! $object instanceof ORMProxy) {
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
