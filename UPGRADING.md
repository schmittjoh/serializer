From 1.12.0 to 2.0.0
====================

Upgrading from 1.x to 2.x should be almost transparent for most of the userland code, 
in case you have heavily used internal-api here are the most important the changes:

**Main changes**

- The minimum PHP version is 7.2, type hints are used almost everywhere, most of the method signatures changed
- `JsonSerializationVisitor::getRoot` and `JsonSerializationVisitor::setRoot` have been removed, their
  use is not necessary anymore
- Removed `AdvancedNamingStrategyInterface`, the serialized name is now compiled and can not be changed at runtime
- "deeper branch group exclusion strategy" has a different behaviour, the latest group is used instead of falling back 
  to "Default" 
- Most of the classes are marked as `final`, inheritance is discouraged for all the cases, use composition instead
- Most of the visor configurations and options have been move to visitor factories
- Removed the abstract classes `GenericSerializationVisito`r and `GenericDeserializationVisitor`.
- Removed deprecated method `VisitorInterface::getNavigator`, use `Context::getNavigator` instead
- Removed deprecated method `JsonSerializationVisitor::addData`, 
  use `:visitProperty(new StaticPropertyMetadata('', 'name', 'value'), null)` instead
- Removed Propel and PhpCollection support
- Changed default date format from ISO8601 to RFC3339  
- Event listeners/handlers class names are case sensitive now
- Removed `AbstractVisitor::getNamingStrategy` method
- Removed Symfony 2.x support
- Removed YAML serialization support
- Removed PHP Driver metadata support
- Removed in-object handler callbacks (`@HandlerCallback` annotation), use event listeners instead
- Changed `SerializerInterface::serialize`  signature
- Changed `ArrayTransformerInterface::toArray` signature
- Changed `GraphNavigator::accept` signature
- Removed `Serializer::setSerializationContextFactory` and `Serializer::setDeserializationContextFactory`
- Removed `Serializer::getMetadataFactory` 
- As default now JSON preserve trailing zeros when serializing a float
- When using a discriminator map, parent class should either be declared abstract, or included into the discriminator
  map

**Deprecations** (will be removed in 3.0)

- `JsonSerializationVisitor::setData` will be removed, 
  use `::visitProperty(new StaticPropertyMetadata('', 'name', 'value'), null)` instead 
- `JsonSerializationVisitor::hasData` will be removed 
- `VisitorInterface` is internal use `SerializationVisitorInterface` and `DeserializationVisitorInterface` instead
- `GraphNavigator` is internal use `GraphNavigatorInterface` instead

**Other**
 - elements (as classes, interfaces, methods, properties...)
  marked as `@internal` shall not be used in user-land code. BC is not guaranteed on this elements.
- PSR4 is used  
- [Here](https://github.com/schmittjoh/serializer/milestone/3) a list of issues and pull requests landed in 2.0
