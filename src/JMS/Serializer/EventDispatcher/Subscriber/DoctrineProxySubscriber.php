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
        $type = $event->getType();

        // If the set type name is not an actual class, but a faked type for which a custom handler exists, we do not
        // modify it with this subscriber. Also, we forgo autoloading here as an instance of this type is already created,
        // so it must be loaded if its a real class.
        $virtualType = ! class_exists($type['name'], false);

        if ($object instanceof PersistentCollection) {
            if ( ! $virtualType) {
                $event->setType('ArrayCollection');
            }

            return;
        }

        if ( ! $object instanceof Proxy && ! $object instanceof ORMProxy) {
            return;
        }

        $object->__load();

        if ( ! $virtualType) {
            $event->setType(get_parent_class($object));
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize'),
        );
    }
}
