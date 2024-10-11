Unreleased
==========

- Use symfony/cache for FileSystem cache implementation instead of doctrine/cache
- Deprecated the `@ReadOnly` annotation due to `readonly` becoming a keyword in PHP 8.1, use the `@ReadOnlyProperty` annotation instead
- Doctrine type `decimal` is now correctly mapped to `string` instead of `float`

From 3.x to 3.30.0
==================

Starting from this release [doctrine/annotations](https://github.com/doctrine/annotations) is an optional package. 
If you still want to use them, please make sure that you require in `composer.json` file.

We strongly recommend to start using [Attributes](https://www.php.net/manual/en/language.attributes.overview.php) with PHP 8. 
You can easily migrate annotations to attributes with [rector](https://github.com/rectorphp/rector) and `Rector\Symfony\Set\SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES` rules.  

From 2.x to 3.0.0
=================

Upgrading from 2.x to 3.x should require almost no effort.

The only change is the revert of "deeper branch group exclusion strategy" introduced in 2.0.0 and now reverted as it
was in 1.x. If you are not using this feature, then upgrading requires no changes at all.

The deprecations introduced in 2.x are still present in 3.0.0, said features are most likely to be removed in the next major version. 

From 1.x to 3.0.0
=================

Please follow the upgrade **"From 1.13.0 to 2.0.0"**, skipping the section:

> "deeper branch group exclusion strategy" has a different behaviour, the latest group is used instead of falling back to "Default"

The deprecations introduced in 2.x are still present in 3.0.0, said features are most likley to be removed in an next major.

From 1.13.0 to 2.0.0
====================

If you are on version `1.x`, it is suggested to migrate directly to `3.0.0` (since `2.x` is not maintained anymore).

**Main changes**

- The minimum PHP version is 7.2, type hints are used almost everywhere, most of the method signatures changed
- `JsonSerializationVisitor::getRoot` and `JsonSerializationVisitor::setRoot` have been removed, their
  use is not necessary anymore
- Removed `AdvancedNamingStrategyInterface`, the serialized name is now compiled and can not be changed at runtime
- "deeper branch group exclusion strategy" has a different behaviour, the latest group is used instead of falling back 
  to "Default" 
- Most of the classes are marked as `final`, inheritance is discouraged for all the cases, use composition instead
- Most of the visitor configurations and options have been moved to visitor factories
- Removed the abstract classes `GenericSerializationVisitor` and `GenericDeserializationVisitor`.
- Removed deprecated method `VisitorInterface::getNavigator`, use `Context::getNavigator` instead
- Removed deprecated method `JsonSerializationVisitor::addData`, 
  use `::visitProperty(new StaticPropertyMetadata('', 'name', 'value'), 'value')` instead
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
- As default now JSON preserves trailing zeros when serializing a float
- When using a discriminator map, parent class should either be declared abstract, or included into the discriminator
  map
- For the `Context` class (and its childs `SerializationContext` and `DeserializationContext`), `$attributes` property has become `private`, so it's no longer accessible; use `getAttribute()` instead
- When implementing custom type handlers and `$context->shouldSerializeNull()` is `false` (it is `false` by default),
  handlers should throw `NotAcceptableException` exception when `null` is visited.
  
  Before:
  ```php
      public function serializeDateTimeToJson(JsonSerializationVisitor $visitor, $data, array $type, Context $context)
      {
        
        // handle custom serialization here
        return $data;
      }
  ```
  
  After:  
  ```php
    public function serializeDateTimeToJson(JsonSerializationVisitor $visitor, $data, array $type, Context $context)
    {
      if (!$context->shouldSerializeNull() && $data === null) {
          throw new NotAcceptableException();
      }
      
      // handle custom serialization here
      return $data;
    }
  ```
   
   

**Deprecations** (will be removed in 4.0)

- `JsonSerializationVisitor::setData` will be removed, 
  use `::visitProperty(new StaticPropertyMetadata('', 'name', 'value'), 'value')` instead 
- `JsonSerializationVisitor::hasData` will be removed 
- `VisitorInterface` is internal, use `SerializationVisitorInterface` and `DeserializationVisitorInterface` instead
- `GraphNavigator` is internal, use `GraphNavigatorInterface` instead
- `enum<'Type'>` and similar are deprecated, use `enum<Type>` instead

**Other**
- Elements (as classes, interfaces, methods, properties...)
  marked as `@internal` shall not be used in user-land code. BC is not guaranteed on this elements.
- PSR-4 is used  
- [Here](https://github.com/schmittjoh/serializer/milestone/3) a list of issues and pull requests landed in 2.0
