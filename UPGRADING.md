From 0.13 to ???
================

- If you have implemented your own ObjectConstructor, you need to add the DeserializationContext as an additional
  parameter for the ``construct`` method.


From 0.11 to 0.12
=================

- GraphNavigator::detachObject has been removed, you can directly use Context::stopVisiting instead.
- VisitorInterface::getNavigator was deprecated, instead use Context::accept
- Serializer::setGroups, Serializer::setExclusionStrategy and Serializer::setVersion were removed, these settings must
  now be passed as part of a new Context object.

    Before:

        $serializer->setVersion(1);
        $serializer->serialize($data, 'json');

    After:

        $serializer->serialize($data, 'json', SerializationContext::create()->setVersion(1));

- All visit??? methods of the VisitorInterface, now require a third argument, the Context; the context is for example
  passed as an additional argument to handlers, exclusion strategies, and also available in event listeners.
