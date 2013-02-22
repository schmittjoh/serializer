From 0.11 to 0.12
=================

- GraphNavigator::detachObject has been removed, you can directly use Context::stopVisiting instead.
- VisitorInterface::getNavigator was deprecated, instead use Context::accept