# Change Log

## [1.13.0](https://github.com/schmittjoh/serializer/tree/1.13.0)

**Implemented enhancements:**

- Bugfix/metadata serialization [\#969](https://github.com/schmittjoh/serializer/pull/969) ([supersmile2009](https://github.com/supersmile2009))

**Fixed bugs:**

- Exception on deserialization using XML and exclude-if [\#975](https://github.com/schmittjoh/serializer/issues/975)

**Closed issues:**

- Serialization fails if root element has custom handler [\#961](https://github.com/schmittjoh/serializer/issues/961)
- Make inline property work with deserialization too [\#937](https://github.com/schmittjoh/serializer/issues/937)

**Merged pull requests:**

- Serializer 2.0 compatibility features [\#967](https://github.com/schmittjoh/serializer/pull/967) ([goetas](https://github.com/goetas))

## [1.12.1](https://github.com/schmittjoh/serializer/tree/1.12.1) (2018-06-01)

**Fixed bugs:**

- Accessing static property as non static [\#960](https://github.com/schmittjoh/serializer/issues/960)
- creating JMS\Serializer\Metadata-\>closureAccessor on internal class failed [\#959](https://github.com/schmittjoh/serializer/issues/959)

## [1.12.0](https://github.com/schmittjoh/serializer/tree/1.12.0) (2018-05-25)

**Implemented enhancements:**

- Add support for namespaced XML attribute on Discriminator + Tests [\#909](https://github.com/schmittjoh/serializer/pull/909) ([ArthurJam](https://github.com/ArthurJam))
- Introduce graph navigator interface [\#876](https://github.com/schmittjoh/serializer/pull/876) ([goetas](https://github.com/goetas))
- Use Bind closure accessor [\#875](https://github.com/schmittjoh/serializer/pull/875) ([goetas](https://github.com/goetas))

**Fixed bugs:**

- DoctrineObjectConstructor and deserialize not work [\#806](https://github.com/schmittjoh/serializer/issues/806)
- \[Symfony\] DoctrineObjectorConstructor always creates new entity because of camel case to snake case conversion [\#734](https://github.com/schmittjoh/serializer/issues/734)
- Fix DoctrineObjectConstructor deserialization with naming strategies [\#951](https://github.com/schmittjoh/serializer/pull/951) ([re2bit](https://github.com/re2bit))

**Closed issues:**

- Feature proposal: dynamic property serialized name [\#225](https://github.com/schmittjoh/serializer/issues/225)
- Mapping request payload works for JSON but not for XML [\#820](https://github.com/schmittjoh/serializer/issues/820)

**Merged pull requests:**

- Cange the spelling of a word [\#939](https://github.com/schmittjoh/serializer/pull/939) ([greg0ire](https://github.com/greg0ire))
- Use dedicated PHPUnit assertions [\#928](https://github.com/schmittjoh/serializer/pull/928) ([carusogabriel](https://github.com/carusogabriel))
- Update arrays.rst [\#907](https://github.com/schmittjoh/serializer/pull/907) ([burki](https://github.com/burki))
- Change to MIT license [\#956](https://github.com/schmittjoh/serializer/pull/956) ([goetas](https://github.com/goetas))
- Double logic for group exclusion \(20% faster\) [\#941](https://github.com/schmittjoh/serializer/pull/941) ([goetas](https://github.com/goetas))
- Type casting tests [\#917](https://github.com/schmittjoh/serializer/pull/917) ([goetas](https://github.com/goetas))
- Explicitly set serialization precision for tests [\#899](https://github.com/schmittjoh/serializer/pull/899) ([Majkl578](https://github.com/Majkl578))
- Deprecations   [\#877](https://github.com/schmittjoh/serializer/pull/877) ([goetas](https://github.com/goetas))
- Added note on SerializedName annotation valididity [\#874](https://github.com/schmittjoh/serializer/pull/874) ([bobvandevijver](https://github.com/bobvandevijver))
- Optimizations [\#861](https://github.com/schmittjoh/serializer/pull/861) ([goetas](https://github.com/goetas))

## [1.11.0](https://github.com/schmittjoh/serializer/tree/1.11.0) (2018-02-04)

**Implemented enhancements:**

- Deserialize xmlKeyValuePairs [\#868](https://github.com/schmittjoh/serializer/pull/868) ([goetas](https://github.com/goetas))
- Add AdvancedNamingStrategyInterface [\#859](https://github.com/schmittjoh/serializer/pull/859) ([LeaklessGfy](https://github.com/LeaklessGfy))
- Deserialize xmlKeyValuePairs [\#840](https://github.com/schmittjoh/serializer/pull/840) ([fdyckhoff](https://github.com/fdyckhoff))

**Fixed bugs:**

- Exception thrown for non-existant accessor to an excluded property [\#862](https://github.com/schmittjoh/serializer/issues/862)
- Support non-namespaced lists in namespaced XML [\#851](https://github.com/schmittjoh/serializer/pull/851) ([bertterheide](https://github.com/bertterheide))

**Closed issues:**

- Context Group not working [\#865](https://github.com/schmittjoh/serializer/issues/865)
- Not all virtual properties are serialized [\#864](https://github.com/schmittjoh/serializer/issues/864)
- DeserializedName [\#857](https://github.com/schmittjoh/serializer/issues/857)
- Annotation does not exist, or could not be auto-loaded. [\#855](https://github.com/schmittjoh/serializer/issues/855)
- \[Question\] Serialization of primitive types [\#853](https://github.com/schmittjoh/serializer/issues/853)
- Empty list when deserializing namespaced XML with children that are not namespaced [\#850](https://github.com/schmittjoh/serializer/issues/850)
- XmlList\(skipWhenEmpty=true\) or @SkipWhenEmpty\(\) does not work [\#847](https://github.com/schmittjoh/serializer/issues/847)
- DateHandler Timezone ignored on deserialization [\#457](https://github.com/schmittjoh/serializer/issues/457)

**Merged pull requests:**

- Drop HHVM support [\#869](https://github.com/schmittjoh/serializer/pull/869) ([goetas](https://github.com/goetas))
- Allow excluded private properties to not have a getter acc… [\#863](https://github.com/schmittjoh/serializer/pull/863) ([0mars](https://github.com/0mars))
- Solve php 7.2 deprecations [\#860](https://github.com/schmittjoh/serializer/pull/860) ([goetas](https://github.com/goetas))
- Fixed issue where timezone is lost when creating DateTime from unix timestamp [\#835](https://github.com/schmittjoh/serializer/pull/835) ([goetas](https://github.com/goetas))

## [1.10.0](https://github.com/schmittjoh/serializer/tree/1.10.0) (2017-11-30)

**Implemented enhancements:**

- support PSR-11 compatible DI containers [\#844](https://github.com/schmittjoh/serializer/pull/844) ([xabbuh](https://github.com/xabbuh))

**Closed issues:**

- Serialize using jsonSerialize\(\) if object implements JsonSerializable [\#846](https://github.com/schmittjoh/serializer/issues/846)
- ExclusionStrategy backward compatibility break [\#843](https://github.com/schmittjoh/serializer/issues/843)
- @MaxDepth jms/serializer-bundle 2.2 [\#842](https://github.com/schmittjoh/serializer/issues/842)

## [1.9.2](https://github.com/schmittjoh/serializer/tree/1.9.2) (2017-11-22)

**Fixed bugs:**

- Missing ClassMetadata deserialization data [\#841](https://github.com/schmittjoh/serializer/pull/841) ([TristanMogwai](https://github.com/TristanMogwai))

**Closed issues:**

- DateTime format documentation [\#836](https://github.com/schmittjoh/serializer/issues/836)
- Deserialization not working with camelCase [\#831](https://github.com/schmittjoh/serializer/issues/831)

**Merged pull requests:**

- Fix documentation syntax errors on available types [\#839](https://github.com/schmittjoh/serializer/pull/839) ([andy-morgan](https://github.com/andy-morgan))
- Improve documentation about default DateTime format [\#838](https://github.com/schmittjoh/serializer/pull/838) ([enumag](https://github.com/enumag))

## [1.9.1](https://github.com/schmittjoh/serializer/tree/1.9.1) (2017-10-27)

**Fixed bugs:**

- Dynamic exclusion strategy, Variable "object" is not valid [\#826](https://github.com/schmittjoh/serializer/issues/826)

**Closed issues:**

- Allow DateTime or Null [\#779](https://github.com/schmittjoh/serializer/issues/779)

**Merged pull requests:**

- Alow to use "object" var in expressions when deserializing [\#827](https://github.com/schmittjoh/serializer/pull/827) ([goetas](https://github.com/goetas))

## [1.9.0](https://github.com/schmittjoh/serializer/tree/1.9.0) (2017-09-28)

**Implemented enhancements:**

- Doctrine LazyCriteriaCollection not supported [\#814](https://github.com/schmittjoh/serializer/issues/814)
- Do not require the translator [\#824](https://github.com/schmittjoh/serializer/pull/824) ([goetas](https://github.com/goetas))
- Added mapping for guid type [\#802](https://github.com/schmittjoh/serializer/pull/802) ([develth](https://github.com/develth))
- Added translation domain to FormErrorHandler [\#783](https://github.com/schmittjoh/serializer/pull/783) ([prosalov](https://github.com/prosalov))

**Fixed bugs:**

-  Node no longer exists - Deserialize Error [\#817](https://github.com/schmittjoh/serializer/issues/817)
- Serializer fails if there is no AnnotationDriver in the DriverChain instance [\#815](https://github.com/schmittjoh/serializer/issues/815)
- Evaluate XML xsi:nil="1" to null  [\#799](https://github.com/schmittjoh/serializer/pull/799) ([Bouwdie](https://github.com/Bouwdie))

**Closed issues:**

- Empty array removed from XML serialization [\#816](https://github.com/schmittjoh/serializer/issues/816)
- XML Discriminator tags don't work in YAML metadata [\#811](https://github.com/schmittjoh/serializer/issues/811)
- Launching phpunit does not execute any test [\#809](https://github.com/schmittjoh/serializer/issues/809)
- Add "bool" Annotation/Type [\#807](https://github.com/schmittjoh/serializer/issues/807)
- Add support for overriding default annotation driver configuration [\#804](https://github.com/schmittjoh/serializer/issues/804)
- Add description to PropertyMetadata? [\#800](https://github.com/schmittjoh/serializer/issues/800)

**Merged pull requests:**

- Workaround to avoid triggering simplexml warning [\#825](https://github.com/schmittjoh/serializer/pull/825) ([goetas](https://github.com/goetas))
- Added null metadata driver [\#822](https://github.com/schmittjoh/serializer/pull/822) ([goetas](https://github.com/goetas))
- Run Travis tests against modern PHP [\#819](https://github.com/schmittjoh/serializer/pull/819) ([Majkl578](https://github.com/Majkl578))
- Added bool type alias [\#818](https://github.com/schmittjoh/serializer/pull/818) ([Majkl578](https://github.com/Majkl578))
- Revert back to PSR-0 [\#797](https://github.com/schmittjoh/serializer/pull/797) ([goetas](https://github.com/goetas))

## [1.8.1](https://github.com/schmittjoh/serializer/tree/1.8.1) (2017-07-13)

**Closed issues:**

- Version 1.8 is breaking backwards compatibility [\#796](https://github.com/schmittjoh/serializer/issues/796)

## [1.8.0](https://github.com/schmittjoh/serializer/tree/1.8.0) (2017-07-12)

**Implemented enhancements:**

- Detect XML xsi:nil="true" to null when deserializing [\#790](https://github.com/schmittjoh/serializer/pull/790) ([goetas](https://github.com/goetas))
- Added support for a third deserialize parameter for the DateTime type [\#788](https://github.com/schmittjoh/serializer/pull/788) ([bobvandevijver](https://github.com/bobvandevijver))
- Added trim to xml metadata reader for groups parameter, and added support for groups element [\#781](https://github.com/schmittjoh/serializer/pull/781) ([mrosiu](https://github.com/mrosiu))
- Add propertyMetdata to dynamic expression variables [\#778](https://github.com/schmittjoh/serializer/pull/778) ([goetas](https://github.com/goetas))
- Fix xml deserialization when xsi:nil="true" is set [\#771](https://github.com/schmittjoh/serializer/pull/771) ([Bouwdie](https://github.com/Bouwdie))

**Fixed bugs:**

- do not disappear type params in DoctrineProxySubscriber [\#793](https://github.com/schmittjoh/serializer/pull/793) ([kriswallsmith](https://github.com/kriswallsmith))
- \#784 fix with inline array of type array\<K, V\> [\#785](https://github.com/schmittjoh/serializer/pull/785) ([aviortm](https://github.com/aviortm))

**Closed issues:**

- inline array with type array\<K, V\> not serialized [\#784](https://github.com/schmittjoh/serializer/issues/784)
- \[2.0\] \[Feature-request\] Provide InitializedObjectConstructor as default [\#775](https://github.com/schmittjoh/serializer/issues/775)
- Allow access to PropertyMetadata in Dynamic Exclusion strategies [\#772](https://github.com/schmittjoh/serializer/issues/772)
- Overriding groups at runtime does not work, or? [\#767](https://github.com/schmittjoh/serializer/issues/767)
- DateTime format and control characters [\#94](https://github.com/schmittjoh/serializer/issues/94)

**Merged pull requests:**

- Missing features of the compiler pass [\#789](https://github.com/schmittjoh/serializer/pull/789) ([mikemix](https://github.com/mikemix))
- Updated documentation related to PR \#778 [\#780](https://github.com/schmittjoh/serializer/pull/780) ([bblue](https://github.com/bblue))
- \[RFC\] Move to PSR 4 [\#770](https://github.com/schmittjoh/serializer/pull/770) ([goetas](https://github.com/goetas))
- Re-formatted code for better PSR compliance [\#769](https://github.com/schmittjoh/serializer/pull/769) ([goetas](https://github.com/goetas))
- Proposing some guidelines for contributing [\#763](https://github.com/schmittjoh/serializer/pull/763) ([goetas](https://github.com/goetas))

## [1.7.1](https://github.com/schmittjoh/serializer/tree/1.7.1) (2017-05-15)

**Fixed bugs:**

- Custom type handlers does not work with doctrine proxies anymore [\#765](https://github.com/schmittjoh/serializer/issues/765)
- Doctrine listener should not change the type on proxies with virtual type [\#768](https://github.com/schmittjoh/serializer/pull/768) ([goetas](https://github.com/goetas))

**Closed issues:**

- Missing bool type in graphNavigator.php in method accept\(\) [\#764](https://github.com/schmittjoh/serializer/issues/764)
- The sub-class "Proxy-Class" is not listed in the discriminator of the base class "DiscriminatorClass" [\#459](https://github.com/schmittjoh/serializer/issues/459)
- Configure whether serializing empty array. [\#124](https://github.com/schmittjoh/serializer/issues/124)

## [1.7.0](https://github.com/schmittjoh/serializer/tree/1.7.0) (2017-05-10)

**Implemented enhancements:**

- Skip doctrine proxy initializations when exclusion strategy will exclude it  [\#760](https://github.com/schmittjoh/serializer/pull/760) ([goetas](https://github.com/goetas))

**Closed issues:**

- Error deserializing a map of \(nullable\) objects [\#762](https://github.com/schmittjoh/serializer/issues/762)
- Add data using setData produces hashes instead of arrays [\#761](https://github.com/schmittjoh/serializer/issues/761)

## [1.7.0-RC2](https://github.com/schmittjoh/serializer/tree/1.7.0-RC2) (2017-05-05)

**Implemented enhancements:**

- Make sure input is always a string [\#755](https://github.com/schmittjoh/serializer/pull/755) ([goetas](https://github.com/goetas))
- Allow namespaced XML element discriminator [\#753](https://github.com/schmittjoh/serializer/pull/753) ([goetas](https://github.com/goetas))

**Fixed bugs:**

- Allow to skip "empty serialization result" when serializing [\#757](https://github.com/schmittjoh/serializer/pull/757) ([goetas](https://github.com/goetas))

**Closed issues:**

- Is it possible to use @XmlNamespace & @XmlRoot in a class at same time ? [\#759](https://github.com/schmittjoh/serializer/issues/759)
- Serializes FOS:User datas with ExclusionPolicy\("all"\)  [\#599](https://github.com/schmittjoh/serializer/issues/599)

**Merged pull requests:**

- Add a quick reference for how to enable expression evaluator [\#758](https://github.com/schmittjoh/serializer/pull/758) ([chasen](https://github.com/chasen))
- Allow for setExpressionEvaluator usage to be chainable [\#756](https://github.com/schmittjoh/serializer/pull/756) ([chasen](https://github.com/chasen))
- Fix typo in annotation docs [\#754](https://github.com/schmittjoh/serializer/pull/754) ([JustBlackBird](https://github.com/JustBlackBird))

## [1.7.0-RC1](https://github.com/schmittjoh/serializer/tree/1.7.0-RC1) (2017-04-25)

**Implemented enhancements:**

- Allow to configure the doctrine object constructor [\#751](https://github.com/schmittjoh/serializer/pull/751) ([goetas](https://github.com/goetas))
- Trigger doctrine events on doctrine proxies [\#750](https://github.com/schmittjoh/serializer/pull/750) ([goetas](https://github.com/goetas))
- Added stdClass serialization handler [\#749](https://github.com/schmittjoh/serializer/pull/749) ([goetas](https://github.com/goetas))

**Fixed bugs:**

- Array gets serialized as object, not as array, depending on order. [\#709](https://github.com/schmittjoh/serializer/issues/709)
- Doctrine Proxies and serializer.pre\_serialize [\#666](https://github.com/schmittjoh/serializer/issues/666)
- Fix stdClass inconsistencies when serializing to JSON [\#730](https://github.com/schmittjoh/serializer/pull/730) ([goetas](https://github.com/goetas))
- Allow to typehint for the type \(array/hash\) of the root item to be serialized [\#728](https://github.com/schmittjoh/serializer/pull/728) ([goetas](https://github.com/goetas))

**Closed issues:**

- Array serialized as JSON object [\#706](https://github.com/schmittjoh/serializer/issues/706)
- From old issue \#290 [\#670](https://github.com/schmittjoh/serializer/issues/670)
- Form validation error response - field names not converted from camel case to underscore [\#587](https://github.com/schmittjoh/serializer/issues/587)
- Ability to getGroups on Context [\#554](https://github.com/schmittjoh/serializer/issues/554)
- SerializedName misleading usage and constructor issue [\#548](https://github.com/schmittjoh/serializer/issues/548)
- Discriminator should support xmlAttribute [\#547](https://github.com/schmittjoh/serializer/issues/547)
- Public method accessor is required for excluded/not exposed properties [\#519](https://github.com/schmittjoh/serializer/issues/519)
- Entity changed via preserialize and wrongly persisted [\#509](https://github.com/schmittjoh/serializer/issues/509)
- XML deserialization properties null when using default namespace [\#504](https://github.com/schmittjoh/serializer/issues/504)
- AccessorOrder is ignored [\#501](https://github.com/schmittjoh/serializer/issues/501)
- Deserialization of sub entites with non existing id [\#492](https://github.com/schmittjoh/serializer/issues/492)
- \[Question\] Handler/Converter for specific field [\#476](https://github.com/schmittjoh/serializer/issues/476)
- getClassName regex may incorrectly retrieve a false class name from comments above the class. [\#460](https://github.com/schmittjoh/serializer/issues/460)
- Multiple types for property? [\#445](https://github.com/schmittjoh/serializer/issues/445)
- Allow non-qualified XML serialization when XML namespaces are part of the metadata [\#413](https://github.com/schmittjoh/serializer/issues/413)
- Discriminator field name [\#412](https://github.com/schmittjoh/serializer/issues/412)
- Serializing to and deserializing from DateTime is inconsistent [\#394](https://github.com/schmittjoh/serializer/issues/394)
- ManyToOne and OneToMany Serialization Groups [\#387](https://github.com/schmittjoh/serializer/issues/387)
- Static SubscribingHandlerInterface::getSubscribingMethod [\#380](https://github.com/schmittjoh/serializer/issues/380)
- User defined ordering function [\#379](https://github.com/schmittjoh/serializer/issues/379)
- serialized\_name for discriminator [\#372](https://github.com/schmittjoh/serializer/issues/372)
- Serializing object with empty array [\#350](https://github.com/schmittjoh/serializer/issues/350)
- VirtualProperty\(s\) are ignored with AccessorOrder [\#349](https://github.com/schmittjoh/serializer/issues/349)
- When setting a group of serialization, the inheritance doesn't work anymore [\#328](https://github.com/schmittjoh/serializer/issues/328)
- Serialization of empty object [\#323](https://github.com/schmittjoh/serializer/issues/323)
- "Can't pop from an empty datastructure" error when multiple serializer calls [\#319](https://github.com/schmittjoh/serializer/issues/319)
- virtual\_properties cannot be excluded with groups [\#291](https://github.com/schmittjoh/serializer/issues/291)
- Integer serialized as String using VirtualProperty [\#289](https://github.com/schmittjoh/serializer/issues/289)
- SimpleObjectProxy is not implement abstract methods of Proxy class [\#287](https://github.com/schmittjoh/serializer/issues/287)
- Serializing array that have one of the element or member of an element an empty object [\#277](https://github.com/schmittjoh/serializer/issues/277)
- Serialization with groups return json object instead array [\#267](https://github.com/schmittjoh/serializer/issues/267)
- The purpose of "Force JSON output to "{}" instead of "\[\]" if it contains either no properties or all properties are null" [\#248](https://github.com/schmittjoh/serializer/issues/248)
- Json array serialisation [\#242](https://github.com/schmittjoh/serializer/issues/242)
- Ignoring "Assert" in output doc if excluded [\#241](https://github.com/schmittjoh/serializer/issues/241)
- Alphabetical accessor order doesn't respect SerializedName overrides [\#240](https://github.com/schmittjoh/serializer/issues/240)
- Request Annotation for Array Data [\#234](https://github.com/schmittjoh/serializer/issues/234)
- Allow @var instead of @Type when deserializing [\#233](https://github.com/schmittjoh/serializer/issues/233)
- Strange issue with groups exclusion strategy [\#230](https://github.com/schmittjoh/serializer/issues/230)
- Warning when deserializing removed entity [\#216](https://github.com/schmittjoh/serializer/issues/216)
- Where in the JMS code does the navigator call VisitProperty method for visitor [\#207](https://github.com/schmittjoh/serializer/issues/207)
- Property of the type array is not in alphabetic order after serialization [\#196](https://github.com/schmittjoh/serializer/issues/196)
- Magic and inconsistencies in array serialization [\#191](https://github.com/schmittjoh/serializer/issues/191)
- PreSerialization Event not handled if the value is not object [\#162](https://github.com/schmittjoh/serializer/issues/162)
- Hierarchical object serialization does not appear to inherit metadata from ancestors for metadata defined in XML [\#151](https://github.com/schmittjoh/serializer/issues/151)
- When using MaxDepth, Serialization of an array entitiy is not working [\#132](https://github.com/schmittjoh/serializer/issues/132)
- Switch to change default naming strategy [\#128](https://github.com/schmittjoh/serializer/issues/128)
- Throw exceptions on invalid input [\#112](https://github.com/schmittjoh/serializer/issues/112)
- Recursion detected error when serialization groups are in use [\#96](https://github.com/schmittjoh/serializer/issues/96)
- Allow serialization groups to be accessible within event subscriber callbacks. [\#84](https://github.com/schmittjoh/serializer/issues/84)
- Allow Constructed Object to be Passed to Deserialize [\#79](https://github.com/schmittjoh/serializer/issues/79)
- JSON recursion when first object in root list is empty [\#61](https://github.com/schmittjoh/serializer/issues/61)
- Can't serialize an array with an empty object [\#59](https://github.com/schmittjoh/serializer/issues/59)

**Merged pull requests:**

- Added runtime twig extension support \(significant performance improvements\) [\#747](https://github.com/schmittjoh/serializer/pull/747) ([goetas](https://github.com/goetas))

## [1.6.2](https://github.com/schmittjoh/serializer/tree/1.6.2) (2017-04-17)

**Fixed bugs:**

- @VirtualProperty "exp" does not play nice with @ExclusionPolicy\("ALL"\) [\#746](https://github.com/schmittjoh/serializer/issues/746)

## [1.6.1](https://github.com/schmittjoh/serializer/tree/1.6.1) (2017-04-12)

**Fixed bugs:**

- Do not output the XML node when the object will be emtpy [\#744](https://github.com/schmittjoh/serializer/pull/744) ([goetas](https://github.com/goetas))

**Closed issues:**

- XmlList not working since version 1.5.0 with xmlns attributes [\#742](https://github.com/schmittjoh/serializer/issues/742)
- DoctrineObjectConstructor: how to use it without Symfony, in a PHP project [\#741](https://github.com/schmittjoh/serializer/issues/741)
- Outdated docs site [\#733](https://github.com/schmittjoh/serializer/issues/733)
- Why do we need this check inside SerializedName constructor, if there is name? [\#558](https://github.com/schmittjoh/serializer/issues/558)
- Is it possible to deserialize Collection from Json [\#534](https://github.com/schmittjoh/serializer/issues/534)
- PhpCollection 0.4 [\#531](https://github.com/schmittjoh/serializer/issues/531)
- Possible mismatch of xml-attribute-map and $pMetadata-\>xmlAttribute in XmlDriver.php [\#422](https://github.com/schmittjoh/serializer/issues/422)
- Access level propose for Handler/DateHandler.php [\#386](https://github.com/schmittjoh/serializer/issues/386)
- Type DateTime and Timestamp \(U format\) [\#343](https://github.com/schmittjoh/serializer/issues/343)

**Merged pull requests:**

- Update PHPDocs [\#736](https://github.com/schmittjoh/serializer/pull/736) ([gnat42](https://github.com/gnat42))

## [1.6.0](https://github.com/schmittjoh/serializer/tree/1.6.0) (2017-03-24)

**Implemented enhancements:**

- Add DateTimeImmutable support to DateHandler [\#543](https://github.com/schmittjoh/serializer/issues/543)

**Fixed bugs:**

- Virtual property having type overriden by doctrine metadata [\#276](https://github.com/schmittjoh/serializer/issues/276)

**Closed issues:**

- Serialize a subclass [\#735](https://github.com/schmittjoh/serializer/issues/735)
- How to handle Doctrine not found entity ? [\#731](https://github.com/schmittjoh/serializer/issues/731)
- Regression with 1.5.0 =\> Undefined offset 15 [\#715](https://github.com/schmittjoh/serializer/issues/715)
- detect serialisation without groups set [\#546](https://github.com/schmittjoh/serializer/issues/546)
- Introducing the NormalizerInterface [\#537](https://github.com/schmittjoh/serializer/issues/537)
- How to set JSON serialization options? [\#535](https://github.com/schmittjoh/serializer/issues/535)
- @MaxDepth doesn't seem to be working [\#522](https://github.com/schmittjoh/serializer/issues/522)
- max\_depth in YML config is ignored [\#498](https://github.com/schmittjoh/serializer/issues/498)
- Dynamic property type  annotation [\#436](https://github.com/schmittjoh/serializer/issues/436)
- How to deserialize JSON if property might have a list of subobjects? [\#355](https://github.com/schmittjoh/serializer/issues/355)
- Object to array normalization [\#354](https://github.com/schmittjoh/serializer/issues/354)
- Serialize Doctrine object without references [\#353](https://github.com/schmittjoh/serializer/issues/353)
- Post\_serialize doesn't serialize relation! [\#236](https://github.com/schmittjoh/serializer/issues/236)
- parsing string to date [\#217](https://github.com/schmittjoh/serializer/issues/217)
- Discriminator is not exposed when using a group exclusion strategy [\#157](https://github.com/schmittjoh/serializer/issues/157)

## [1.6.0-RC1](https://github.com/schmittjoh/serializer/tree/1.6.0-RC1) (2017-03-14)

**Implemented enhancements:**

- Add symfony expression in exclusions/expositions [\#406](https://github.com/schmittjoh/serializer/issues/406)
- check that cache directory was not created before throwing exception [\#729](https://github.com/schmittjoh/serializer/pull/729) ([mente](https://github.com/mente))
- \#720 - Adding support for DateInterval deserialization [\#721](https://github.com/schmittjoh/serializer/pull/721) ([c0ntax](https://github.com/c0ntax))
- Expression language based virtual properties [\#708](https://github.com/schmittjoh/serializer/pull/708) ([goetas](https://github.com/goetas))
- Added clearing previous libxml errors [\#688](https://github.com/schmittjoh/serializer/pull/688) ([zerkms](https://github.com/zerkms))
- Xml namespaces improvements [\#644](https://github.com/schmittjoh/serializer/pull/644) ([goetas](https://github.com/goetas))

**Fixed bugs:**

- Serialize correctly empty intervals according to ISO-8601 [\#722](https://github.com/schmittjoh/serializer/pull/722) ([goetas](https://github.com/goetas))

**Closed issues:**

- Is it possible to achieve something like - shouldSerializeEmpty  [\#725](https://github.com/schmittjoh/serializer/issues/725)
- How to handle DateTime serialization with fromArray method ? [\#723](https://github.com/schmittjoh/serializer/issues/723)
- DateInterval supported for serialization but not deserialization [\#720](https://github.com/schmittjoh/serializer/issues/720)
- Deserialization of collection when wraped by aditional xml tags [\#719](https://github.com/schmittjoh/serializer/issues/719)
- SerializedName based on a property value [\#716](https://github.com/schmittjoh/serializer/issues/716)
- Blank XML breaks XmlDeserializationVisitor error handling [\#701](https://github.com/schmittjoh/serializer/issues/701)
- Problem with FOSUserBundle ROLE serialization [\#690](https://github.com/schmittjoh/serializer/issues/690)
- Doctrine cache dependency when using setCacheDir [\#676](https://github.com/schmittjoh/serializer/issues/676)
- OneToOne entities are not deserialized if passing a nested one-to-one property [\#652](https://github.com/schmittjoh/serializer/issues/652)
- \[RFC\] Serialization refacotring [\#609](https://github.com/schmittjoh/serializer/issues/609)
- Object handler callback returns array, but serialized object = null [\#594](https://github.com/schmittjoh/serializer/issues/594)
- Cannot add @Discriminator field into specific @Group [\#557](https://github.com/schmittjoh/serializer/issues/557)
- Object check on SerializationContext::isVisiting\(\) [\#502](https://github.com/schmittjoh/serializer/issues/502)
-  Define cdata and namespace for @XmlList elements [\#480](https://github.com/schmittjoh/serializer/issues/480)
- Serializer working with parent class [\#376](https://github.com/schmittjoh/serializer/issues/376)
- Add support for array format [\#374](https://github.com/schmittjoh/serializer/issues/374)
- Obtain VirtualProperty value using a service [\#359](https://github.com/schmittjoh/serializer/issues/359)
- make deserialisation of non existing id's configurable [\#333](https://github.com/schmittjoh/serializer/issues/333)
- HHVM compatibility issue with undefined property JMS\Serializer\Metadata\ClassMetadata::$inline  [\#312](https://github.com/schmittjoh/serializer/issues/312)
- resources serialization [\#275](https://github.com/schmittjoh/serializer/issues/275)
- I'm receiving "Class ArrayCollection does not exist" when serializing \(temporarily solved with a workaround\) [\#274](https://github.com/schmittjoh/serializer/issues/274)
- Can't use handlers on strings \(and other simple types\) [\#194](https://github.com/schmittjoh/serializer/issues/194)
- composer.json update for doctrine [\#178](https://github.com/schmittjoh/serializer/issues/178)
- Use expression for virtual properties [\#171](https://github.com/schmittjoh/serializer/issues/171)
- Handle classes that implement collections \(e.g. ArrayObject\) and properties [\#137](https://github.com/schmittjoh/serializer/issues/137)
- Check CDATA is needed [\#136](https://github.com/schmittjoh/serializer/issues/136)
- property path support [\#22](https://github.com/schmittjoh/serializer/issues/22)

**Merged pull requests:**

- Include reference to cache [\#727](https://github.com/schmittjoh/serializer/pull/727) ([hyperized](https://github.com/hyperized))
- A possible fix for the \#688 [\#689](https://github.com/schmittjoh/serializer/pull/689) ([zerkms](https://github.com/zerkms))

## [1.5.0](https://github.com/schmittjoh/serializer/tree/1.5.0) (2017-02-14)

**Fixed bugs:**

- Deserializing XMLList with Namespaces not \(always\) working as intended [\#697](https://github.com/schmittjoh/serializer/pull/697) ([goetas](https://github.com/goetas))

**Closed issues:**

- Serialized DateTime instances are not valid ISO-8601 [\#713](https://github.com/schmittjoh/serializer/issues/713)
- Impossible to use discriminator field. Why we need StaticPropertyMetadata ? [\#705](https://github.com/schmittjoh/serializer/issues/705)
- Deserializing XMLList with Namespaces not \(always\) working as intended [\#695](https://github.com/schmittjoh/serializer/issues/695)

## [1.5.0-RC1](https://github.com/schmittjoh/serializer/tree/1.5.0-RC1) (2017-01-19)

**Implemented enhancements:**

- added support for xml-attributes as discriminators [\#692](https://github.com/schmittjoh/serializer/pull/692) ([twtinteractive](https://github.com/twtinteractive))
- Prevent doctrine proxy loading for virtual types [\#684](https://github.com/schmittjoh/serializer/pull/684) ([goetas](https://github.com/goetas))
- Implemented dynamic exclusion using symfony expression language [\#673](https://github.com/schmittjoh/serializer/pull/673) ([goetas](https://github.com/goetas))
- Issue543 - Adding DateTimeImmutable support [\#635](https://github.com/schmittjoh/serializer/pull/635) ([toby-griffiths](https://github.com/toby-griffiths))

**Closed issues:**

- Groups logic [\#693](https://github.com/schmittjoh/serializer/issues/693)
- BC from 1.1.\* to ^1.2 [\#643](https://github.com/schmittjoh/serializer/issues/643)
- DoctrineProxySubscriber forces loading of the proxy even if custom handler exist [\#575](https://github.com/schmittjoh/serializer/issues/575)
- ConditionalExpose/Exclude annotation [\#540](https://github.com/schmittjoh/serializer/issues/540)
- Deprecated usage of ValidatorInterface [\#438](https://github.com/schmittjoh/serializer/issues/438)
- Missing addData in XmlSerializerVisitor makes it impossible to add data in serializer.post\_serialize event [\#235](https://github.com/schmittjoh/serializer/issues/235)
- Support JSON PATCH for updating object graph [\#231](https://github.com/schmittjoh/serializer/issues/231)
- Dynamic expose, aka 'fields' query param [\#195](https://github.com/schmittjoh/serializer/issues/195)

**Merged pull requests:**

- Added doc reference for disabling discriminator [\#699](https://github.com/schmittjoh/serializer/pull/699) ([dragosprotung](https://github.com/dragosprotung))
- Use GroupsExclusionStrategy::DEFAULT\_GROUP instead default group. [\#694](https://github.com/schmittjoh/serializer/pull/694) ([Aliance](https://github.com/Aliance))
- Improved Symfony 3.x compatibility  [\#682](https://github.com/schmittjoh/serializer/pull/682) ([goetas](https://github.com/goetas))
- Discriminator Groups [\#579](https://github.com/schmittjoh/serializer/pull/579) ([maennchen](https://github.com/maennchen))
- Add extra test for handling child elements [\#569](https://github.com/schmittjoh/serializer/pull/569) ([tarjei](https://github.com/tarjei))

## [1.4.2](https://github.com/schmittjoh/serializer/tree/1.4.2) (2016-11-13)

**Fixed bugs:**

- Warning: JMS\Serializer\XmlDeserializationVisitor::visitArray\(\): Node no longer exists [\#674](https://github.com/schmittjoh/serializer/issues/674)
- Fixed xml arrays with namespaced entry triggers error [\#675](https://github.com/schmittjoh/serializer/pull/675) ([goetas](https://github.com/goetas))

**Closed issues:**

- Max depth produces array of nulls [\#671](https://github.com/schmittjoh/serializer/issues/671)

## [1.4.1](https://github.com/schmittjoh/serializer/tree/1.4.1) (2016-11-02)

**Fixed bugs:**

- Groups context might be not initialized  [\#669](https://github.com/schmittjoh/serializer/pull/669) ([goetas](https://github.com/goetas))

**Closed issues:**

- Warning: Invalid argument supplied for foreach\(\) on getCurrentPath method [\#668](https://github.com/schmittjoh/serializer/issues/668)

## [1.4.0](https://github.com/schmittjoh/serializer/tree/1.4.0) (2016-10-31)

**Implemented enhancements:**

- Document the implied 'Default' property group when no group is specified [\#661](https://github.com/schmittjoh/serializer/pull/661) ([akoebbe](https://github.com/akoebbe))
- Allow discriminator map in the middle of the hierarchy when deserializing [\#659](https://github.com/schmittjoh/serializer/pull/659) ([goetas](https://github.com/goetas))
- Handle both int and integer [\#657](https://github.com/schmittjoh/serializer/pull/657) ([Aliance](https://github.com/Aliance))
- Can now override groups on specific paths of the graph [\#170](https://github.com/schmittjoh/serializer/pull/170) ([adrienbrault](https://github.com/adrienbrault))

**Fixed bugs:**

- Deserialization fails when discriminator base class extends another class [\#182](https://github.com/schmittjoh/serializer/issues/182)
- Xml setters ignored when deserializing [\#665](https://github.com/schmittjoh/serializer/pull/665) ([goetas](https://github.com/goetas))

**Closed issues:**

- Move `FormErrorHandler` to the bundle [\#664](https://github.com/schmittjoh/serializer/issues/664)
- Not compatible with Symfony 3's Controller::json\(\) [\#663](https://github.com/schmittjoh/serializer/issues/663)
- Class name not reflecting in serialized json [\#662](https://github.com/schmittjoh/serializer/issues/662)
- YML virtual\_properties no group exlcusion [\#656](https://github.com/schmittjoh/serializer/issues/656)
- \[RFC\] Introduce normalizer\denormalizer interface [\#646](https://github.com/schmittjoh/serializer/issues/646)
- Plain arrays are serialized \(normalized\) as "objects", ignoring serializeNull [\#641](https://github.com/schmittjoh/serializer/issues/641)
- serializer doesn't serialize traits [\#638](https://github.com/schmittjoh/serializer/issues/638)
- Add metadata informations [\#637](https://github.com/schmittjoh/serializer/issues/637)
- Unexpected results when serializing arrays containing null value elements  [\#593](https://github.com/schmittjoh/serializer/issues/593)
- Allow to set default serialization context when building serializer [\#528](https://github.com/schmittjoh/serializer/issues/528)
- Enable Sourcegraph [\#455](https://github.com/schmittjoh/serializer/issues/455)
- Use different accessor for each group [\#420](https://github.com/schmittjoh/serializer/issues/420)
- GenericSerializationVisitor and shouldSerializeNull [\#360](https://github.com/schmittjoh/serializer/issues/360)
- Specify group along with MaxDepth [\#150](https://github.com/schmittjoh/serializer/issues/150)
- Allow Post Serialize Event to overwrite existing data [\#129](https://github.com/schmittjoh/serializer/issues/129)
- Warning: array\_key\_exists\(\) expects parameter 2 to be array, string given [\#70](https://github.com/schmittjoh/serializer/issues/70)

**Merged pull requests:**

- Nullable array inconsistency [\#660](https://github.com/schmittjoh/serializer/pull/660) ([goetas](https://github.com/goetas))
- Fixed PHP 7.0.11 BC break \(or bugfix\) [\#658](https://github.com/schmittjoh/serializer/pull/658) ([goetas](https://github.com/goetas))
- Renamed replaceData to setData [\#653](https://github.com/schmittjoh/serializer/pull/653) ([goetas](https://github.com/goetas))
- add required sqlite extension for developing [\#649](https://github.com/schmittjoh/serializer/pull/649) ([scasei](https://github.com/scasei))
- Run serialization benchmarks in the build process [\#647](https://github.com/schmittjoh/serializer/pull/647) ([goetas](https://github.com/goetas))
- Alcalyn feature default serializer context [\#645](https://github.com/schmittjoh/serializer/pull/645) ([goetas](https://github.com/goetas))
- Add format output option [\#640](https://github.com/schmittjoh/serializer/pull/640) ([AyrtonRicardo](https://github.com/AyrtonRicardo))
- Remove deprecated FileCacheReader for doctrine annotations [\#634](https://github.com/schmittjoh/serializer/pull/634) ([goetas](https://github.com/goetas))
- Added tests to ensure SerializeNull policy [\#633](https://github.com/schmittjoh/serializer/pull/633) ([goetas](https://github.com/goetas))
- Revert "Default `$serializeNull` to false" [\#630](https://github.com/schmittjoh/serializer/pull/630) ([goetas](https://github.com/goetas))
- Introducing NormalizerInterface [\#592](https://github.com/schmittjoh/serializer/pull/592) ([alcalyn](https://github.com/alcalyn))
- Fix inheritance of discriminators on Doctrine entities [\#382](https://github.com/schmittjoh/serializer/pull/382) ([xoob](https://github.com/xoob))
- Allow Post Serialize Event to overwrite existing data [\#273](https://github.com/schmittjoh/serializer/pull/273) ([jockri](https://github.com/jockri))

## [1.3.1](https://github.com/schmittjoh/serializer/tree/1.3.1) (2016-08-23)

**Closed issues:**

- \[Idea\] Inline name [\#629](https://github.com/schmittjoh/serializer/issues/629)
- indexBy property doesn't work since 1.2.0 [\#618](https://github.com/schmittjoh/serializer/issues/618)
- Composer deps issue [\#494](https://github.com/schmittjoh/serializer/issues/494)
- PHP 7 compatability issue [\#478](https://github.com/schmittjoh/serializer/issues/478)
- Add new tag \(upgrade packagist\) [\#461](https://github.com/schmittjoh/serializer/issues/461)
- Custom Type Handler for String Values [\#384](https://github.com/schmittjoh/serializer/issues/384)
- serializer ignores properties added by traits [\#313](https://github.com/schmittjoh/serializer/issues/313)
- Skip an element during Xml deserialization process [\#229](https://github.com/schmittjoh/serializer/issues/229)
- Using serializer for JSON serialising [\#223](https://github.com/schmittjoh/serializer/issues/223)
- No way to serialize binary data with a custom type [\#202](https://github.com/schmittjoh/serializer/issues/202)
- Automatic mapping of properties [\#200](https://github.com/schmittjoh/serializer/issues/200)
- Maybe the serializer should also allow the legal literals {1, 0} for booleans [\#198](https://github.com/schmittjoh/serializer/issues/198)
- Customize how Booleans are serialized [\#180](https://github.com/schmittjoh/serializer/issues/180)
- Problem with deserialize related entity [\#123](https://github.com/schmittjoh/serializer/issues/123)
- serialized\_name does not work in yaml [\#118](https://github.com/schmittjoh/serializer/issues/118)

## [1.3.0](https://github.com/schmittjoh/serializer/tree/1.3.0) (2016-08-17)

**Fixed bugs:**

- Fix warning array\_key\_exists in deserialization. [\#398](https://github.com/schmittjoh/serializer/pull/398) ([leonnleite](https://github.com/leonnleite))

**Closed issues:**

- problems with xml namespaces after update [\#621](https://github.com/schmittjoh/serializer/issues/621)
- Trying to decorate a member to ArrayCollection but gets an error when deserilizing because composer didn't download the class from doctrine. [\#596](https://github.com/schmittjoh/serializer/issues/596)
- Missing doctrine/common requirement ? [\#517](https://github.com/schmittjoh/serializer/issues/517)
- PHP Fatal error: Using $this when not in object context in JMS/Serializer/Serializer.php on line 99 [\#441](https://github.com/schmittjoh/serializer/issues/441)
- custom collection handler [\#415](https://github.com/schmittjoh/serializer/issues/415)
- Exclude annotation not preventing attempt to find public methods when using AccessType [\#367](https://github.com/schmittjoh/serializer/issues/367)
- serializer.pre\_serialize event only thrown on objects/classes [\#337](https://github.com/schmittjoh/serializer/issues/337)
- Installing through composer gets "Segmentation fault" [\#308](https://github.com/schmittjoh/serializer/issues/308)
- Erroneous data format for unserializing... [\#283](https://github.com/schmittjoh/serializer/issues/283)
- DoctrineObjectConstructor should skip empty identifier field [\#193](https://github.com/schmittjoh/serializer/issues/193)

**Merged pull requests:**

- Added public `hasData` function to check if a data key already have been added. [\#625](https://github.com/schmittjoh/serializer/pull/625) ([goetas](https://github.com/goetas))
- $context is not used [\#622](https://github.com/schmittjoh/serializer/pull/622) ([olvlvl](https://github.com/olvlvl))
- Fix Doctrine PHPCR ODM 2.0 compatibility [\#605](https://github.com/schmittjoh/serializer/pull/605) ([wouterj](https://github.com/wouterj))
- Fixed type-hinting [\#586](https://github.com/schmittjoh/serializer/pull/586) ([jgendera](https://github.com/jgendera))
- Fix multiple handler callbacks in YamlDriver [\#515](https://github.com/schmittjoh/serializer/pull/515) ([mpajunen](https://github.com/mpajunen))
- Fixed minor typos [\#364](https://github.com/schmittjoh/serializer/pull/364) ([sdaoudi](https://github.com/sdaoudi))
- Default `$serializeNull` to false [\#317](https://github.com/schmittjoh/serializer/pull/317) ([steveYeah](https://github.com/steveYeah))
- Missing attribute 'xml-value' in XML Reference [\#269](https://github.com/schmittjoh/serializer/pull/269) ([holtkamp](https://github.com/holtkamp))
- Removed unnecessary use statement [\#262](https://github.com/schmittjoh/serializer/pull/262) ([dunglas](https://github.com/dunglas))

## [1.2.0](https://github.com/schmittjoh/serializer/tree/1.2.0) (2016-08-03)

**Fixed bugs:**

- Fix xml-attribute-map for the xml driver [\#595](https://github.com/schmittjoh/serializer/pull/595) ([romantomchak](https://github.com/romantomchak))
- \#367 Exclude annotation not preventing attempt to find public methods when using AccessType [\#397](https://github.com/schmittjoh/serializer/pull/397) ([Strate](https://github.com/Strate))

**Closed issues:**

- XML serialisation performance vs. SimpleXML? [\#606](https://github.com/schmittjoh/serializer/issues/606)
- Undefined Offset 21 - PropertyMetadata \(master\) [\#581](https://github.com/schmittjoh/serializer/issues/581)
- Invalid null serialization in arrays [\#571](https://github.com/schmittjoh/serializer/issues/571)
- List Polymorphic with XML Deserialization [\#568](https://github.com/schmittjoh/serializer/issues/568)
- Serialize null values as empty string [\#566](https://github.com/schmittjoh/serializer/issues/566)
- Type mismatch should throw an exception instead of coercing when deserializing JSON [\#561](https://github.com/schmittjoh/serializer/issues/561)
- Serialize to array [\#518](https://github.com/schmittjoh/serializer/issues/518)
- AnnotationDriver Exception on Missing Setter/Getter even on @Exclude'd Properties [\#516](https://github.com/schmittjoh/serializer/issues/516)
- Arrays are serialized as objects like {"0":... } when data contains empty objects [\#488](https://github.com/schmittjoh/serializer/issues/488)
- Tag new release [\#465](https://github.com/schmittjoh/serializer/issues/465)
- Forcing no scientific notation for larg number, type double [\#405](https://github.com/schmittjoh/serializer/issues/405)
- PHP \< 5.3.9 BC break [\#383](https://github.com/schmittjoh/serializer/issues/383)
- Ignoring a tag when deserializing [\#352](https://github.com/schmittjoh/serializer/issues/352)

**Merged pull requests:**

- Allow to not skip empty not inline array root node [\#611](https://github.com/schmittjoh/serializer/pull/611) ([goetas](https://github.com/goetas))
- Allow to use custom serializer with primitive type [\#610](https://github.com/schmittjoh/serializer/pull/610) ([goetas](https://github.com/goetas))
- Composer is not able to resolve a dependency [\#608](https://github.com/schmittjoh/serializer/pull/608) ([goetas](https://github.com/goetas))
- Test on Travis always high and low deps [\#584](https://github.com/schmittjoh/serializer/pull/584) ([goetas](https://github.com/goetas))
- Update Symfony validator and allow PHPUnit 7 [\#583](https://github.com/schmittjoh/serializer/pull/583) ([goetas](https://github.com/goetas))
- Fix serialize bug [\#582](https://github.com/schmittjoh/serializer/pull/582) ([goetas](https://github.com/goetas))
- HHVM compatibility [\#580](https://github.com/schmittjoh/serializer/pull/580) ([goetas](https://github.com/goetas))
- Fixed test suite on master [\#578](https://github.com/schmittjoh/serializer/pull/578) ([goetas](https://github.com/goetas))
- Fix for a broken test: a missing \(incorrectly positioned\) argument [\#577](https://github.com/schmittjoh/serializer/pull/577) ([zerkms](https://github.com/zerkms))
- Fix bug \#343 return integer when the column is datetime [\#562](https://github.com/schmittjoh/serializer/pull/562) ([Bukashk0zzz](https://github.com/Bukashk0zzz))
- \[doc\] fix AccessorOrder documentation [\#553](https://github.com/schmittjoh/serializer/pull/553) ([aledeg](https://github.com/aledeg))
- Generic way to solve setValue on a property which respects its setter [\#550](https://github.com/schmittjoh/serializer/pull/550) ([maennchen](https://github.com/maennchen))
- Added travis-ci label [\#399](https://github.com/schmittjoh/serializer/pull/399) ([spolischook](https://github.com/spolischook))
- Generate namespaced element on XmlList entries [\#301](https://github.com/schmittjoh/serializer/pull/301) ([goetas](https://github.com/goetas))

## [1.1.0](https://github.com/schmittjoh/serializer/tree/1.1.0) (2015-10-27)

**Closed issues:**

- Possible to set xsi:schemalocation? [\#505](https://github.com/schmittjoh/serializer/issues/505)
- Travis needs a renewed token to be able to set the status [\#495](https://github.com/schmittjoh/serializer/issues/495)
- Serialize a many-to-many relation [\#474](https://github.com/schmittjoh/serializer/issues/474)
- The document type "..." is not allowed [\#427](https://github.com/schmittjoh/serializer/issues/427)
- Yml serializer don't serialize empty arrays [\#183](https://github.com/schmittjoh/serializer/issues/183)

**Merged pull requests:**

- Manage empty array for serializer [\#510](https://github.com/schmittjoh/serializer/pull/510) ([Soullivaneuh](https://github.com/Soullivaneuh))
- Fix the method name for the serialization context factory [\#490](https://github.com/schmittjoh/serializer/pull/490) ([stof](https://github.com/stof))
- Switch the Twig integration to use non-deprecated APIs [\#482](https://github.com/schmittjoh/serializer/pull/482) ([stof](https://github.com/stof))
- Add PHP 7 on Travis [\#477](https://github.com/schmittjoh/serializer/pull/477) ([Soullivaneuh](https://github.com/Soullivaneuh))
- Change Proxy class used to Doctrine\Common\Persistence\Proxy [\#351](https://github.com/schmittjoh/serializer/pull/351) ([bburnichon](https://github.com/bburnichon))
- Added PHP 5.6 [\#297](https://github.com/schmittjoh/serializer/pull/297) ([Nyholm](https://github.com/Nyholm))

## [1.0.0](https://github.com/schmittjoh/serializer/tree/1.0.0) (2015-06-16)

**Closed issues:**

- Unrecognized 4 parts namespace [\#449](https://github.com/schmittjoh/serializer/issues/449)
- Groups is ignored [\#440](https://github.com/schmittjoh/serializer/issues/440)
-  Property FelDev\CoreBundle\Entity\Persona::$apellido does not exist  [\#432](https://github.com/schmittjoh/serializer/issues/432)
- Erroneous data format for unserializing [\#430](https://github.com/schmittjoh/serializer/issues/430)
- Deserialize JSON into existing Doctrine entities and empty strings are ignored [\#417](https://github.com/schmittjoh/serializer/issues/417)
- Failing to deserealize JSON string [\#402](https://github.com/schmittjoh/serializer/issues/402)
- Empty results serializing virtual\_properties [\#400](https://github.com/schmittjoh/serializer/issues/400)
- API stable 1.0.0 release in sight? [\#395](https://github.com/schmittjoh/serializer/issues/395)
- Is this project maintained still? [\#361](https://github.com/schmittjoh/serializer/issues/361)
- PreSerialize [\#339](https://github.com/schmittjoh/serializer/issues/339)
- Change default `access\_type` globally [\#336](https://github.com/schmittjoh/serializer/issues/336)
- Deserialization of XmlList does not support namespaces [\#332](https://github.com/schmittjoh/serializer/issues/332)
- Recursion groups, serializing properties in entities [\#329](https://github.com/schmittjoh/serializer/issues/329)
- The testsuite is broken [\#326](https://github.com/schmittjoh/serializer/issues/326)
- Namespaces and serialize/deserialize process [\#303](https://github.com/schmittjoh/serializer/issues/303)
- Exclusion of parent properties failing [\#282](https://github.com/schmittjoh/serializer/issues/282)
- How to deserialize correctly an array of arbitrary values ? [\#280](https://github.com/schmittjoh/serializer/issues/280)
- Try to identify getter/setter from an excluded property [\#278](https://github.com/schmittjoh/serializer/issues/278)
- Bug Entity constructor not called [\#270](https://github.com/schmittjoh/serializer/issues/270)
- Make it possible to escape special characters on serialization [\#265](https://github.com/schmittjoh/serializer/issues/265)
- doctrine annotations without namespace [\#264](https://github.com/schmittjoh/serializer/issues/264)
- php-collection constraint [\#257](https://github.com/schmittjoh/serializer/issues/257)
- \[Metadata\] PHP warning only when unittesting [\#255](https://github.com/schmittjoh/serializer/issues/255)
- Discriminator [\#220](https://github.com/schmittjoh/serializer/issues/220)

**Merged pull requests:**

- fix json output \(from \[\] to {} if empty\) of form error [\#462](https://github.com/schmittjoh/serializer/pull/462) ([jhkchan](https://github.com/jhkchan))
- Add toArray and fromArray methods to the serializer [\#435](https://github.com/schmittjoh/serializer/pull/435) ([tystr](https://github.com/tystr))
- Erroneous data format for unserializing \#430 [\#431](https://github.com/schmittjoh/serializer/pull/431) ([tmilos](https://github.com/tmilos))
- Scrutinizer Auto-Fixes [\#381](https://github.com/schmittjoh/serializer/pull/381) ([scrutinizer-auto-fixer](https://github.com/scrutinizer-auto-fixer))
- Fixing tests for bugfixed PHP versions [\#375](https://github.com/schmittjoh/serializer/pull/375) ([urakozz](https://github.com/urakozz))
- Making test running against phpunit 4.\* [\#369](https://github.com/schmittjoh/serializer/pull/369) ([joelwurtz](https://github.com/joelwurtz))
- Fixes a typo in the annotations.rst [\#363](https://github.com/schmittjoh/serializer/pull/363) ([Potherca](https://github.com/Potherca))
- \[doc\] Default group informations [\#345](https://github.com/schmittjoh/serializer/pull/345) ([emilien-puget](https://github.com/emilien-puget))
- bump branch alias to 0.17 as 0.16 is already released [\#305](https://github.com/schmittjoh/serializer/pull/305) ([lsmith77](https://github.com/lsmith77))
- Unserialization of XML booleans [\#302](https://github.com/schmittjoh/serializer/pull/302) ([goetas](https://github.com/goetas))
- Added xml\_root\_namespace on YAML reference [\#299](https://github.com/schmittjoh/serializer/pull/299) ([goetas](https://github.com/goetas))
- Fixed yml mapping file name [\#256](https://github.com/schmittjoh/serializer/pull/256) ([spolischook](https://github.com/spolischook))
- Serialization of nested polymorphic objects [\#238](https://github.com/schmittjoh/serializer/pull/238) ([DavidMikeSimon](https://github.com/DavidMikeSimon))

## [0.16.0](https://github.com/schmittjoh/serializer/tree/0.16.0) (2014-03-18)

**Closed issues:**

- best way to add root to json?  [\#250](https://github.com/schmittjoh/serializer/issues/250)
- Use Doctrine metadata [\#247](https://github.com/schmittjoh/serializer/issues/247)
- Integration Points - run-time exclusion checking [\#239](https://github.com/schmittjoh/serializer/issues/239)
- Using DoctrineTypeDriver to use Doctrine Anotations  [\#232](https://github.com/schmittjoh/serializer/issues/232)
- Virtual property documentation xml & yaml [\#100](https://github.com/schmittjoh/serializer/issues/100)

**Merged pull requests:**

- Changed some constraint to allow latest versions [\#251](https://github.com/schmittjoh/serializer/pull/251) ([stof](https://github.com/stof))
- XML root element namespace support [\#246](https://github.com/schmittjoh/serializer/pull/246) ([andreasferber](https://github.com/andreasferber))
- Added test for leading backslash in front of class name to TypeParserTest [\#245](https://github.com/schmittjoh/serializer/pull/245) ([deralex](https://github.com/deralex))
- Allow to fetch data from has\*\(\) with public\_method [\#243](https://github.com/schmittjoh/serializer/pull/243) ([jaymecd](https://github.com/jaymecd))
- Improve yaml documentacion Fix \#100 [\#221](https://github.com/schmittjoh/serializer/pull/221) ([BraisGabin](https://github.com/BraisGabin))

## [0.15.0](https://github.com/schmittjoh/serializer/tree/0.15.0) (2014-02-10)

**Closed issues:**

- Add trait support [\#228](https://github.com/schmittjoh/serializer/issues/228)
- "array" type: Not working for arrays of DateTime objects [\#199](https://github.com/schmittjoh/serializer/issues/199)
- Discriminator field filtered by exclusion strategy [\#189](https://github.com/schmittjoh/serializer/issues/189)
- DateTime within an array \(format get ignored\) [\#140](https://github.com/schmittjoh/serializer/issues/140)
- EntityNotFoundException using softDeletable [\#101](https://github.com/schmittjoh/serializer/issues/101)

**Merged pull requests:**

- Read only class [\#227](https://github.com/schmittjoh/serializer/pull/227) ([goetas](https://github.com/goetas))
- @Alex88's Serialize only form child of type Form \#117  [\#224](https://github.com/schmittjoh/serializer/pull/224) ([minayaserrano](https://github.com/minayaserrano))
- @XmlElement notation consistency [\#219](https://github.com/schmittjoh/serializer/pull/219) ([ajgarlag](https://github.com/ajgarlag))
- add $this-\>maxDepth to serialize / unserialize [\#218](https://github.com/schmittjoh/serializer/pull/218) ([rothfahl](https://github.com/rothfahl))
- xml reference updated with virtual-property example [\#215](https://github.com/schmittjoh/serializer/pull/215) ([ribeiropaulor](https://github.com/ribeiropaulor))
- Add XmlNamespace annotation documentation [\#213](https://github.com/schmittjoh/serializer/pull/213) ([jeserkin](https://github.com/jeserkin))
- Scrutinizer Auto-Fixes [\#210](https://github.com/schmittjoh/serializer/pull/210) ([scrutinizer-auto-fixer](https://github.com/scrutinizer-auto-fixer))
- Scrutinizer Auto-Fixes [\#206](https://github.com/schmittjoh/serializer/pull/206) ([scrutinizer-auto-fixer](https://github.com/scrutinizer-auto-fixer))
- Add xmlAttributeMap to serialized values [\#204](https://github.com/schmittjoh/serializer/pull/204) ([colinfrei](https://github.com/colinfrei))
- fix issue \#199: "array" type ignoring DateTime format [\#201](https://github.com/schmittjoh/serializer/pull/201) ([lukey78](https://github.com/lukey78))
- Potential fix for "recursion detected" issue [\#104](https://github.com/schmittjoh/serializer/pull/104) ([tyler-sommer](https://github.com/tyler-sommer))
- Adds XML namespaces support [\#58](https://github.com/schmittjoh/serializer/pull/58) ([ajgarlag](https://github.com/ajgarlag))

## [0.14.0](https://github.com/schmittjoh/serializer/tree/0.14.0) (2013-12-04)

**Closed issues:**

- @HandlerCallback not inherited [\#181](https://github.com/schmittjoh/serializer/issues/181)
- Conditional serialization [\#173](https://github.com/schmittjoh/serializer/issues/173)
- Deserialize XML partially [\#167](https://github.com/schmittjoh/serializer/issues/167)
- getter is not called when serializing Discriminator parent entity [\#156](https://github.com/schmittjoh/serializer/issues/156)
- Deserialize DateTime from js Date.toJSON format fail [\#145](https://github.com/schmittjoh/serializer/issues/145)
- Yaml driver for the parameter xml\_attribute\_map is broken [\#141](https://github.com/schmittjoh/serializer/issues/141)
- XmlKeyValueStore annotation does not seem to deserialize properly [\#139](https://github.com/schmittjoh/serializer/issues/139)
- Boolean conversion gone wrong [\#134](https://github.com/schmittjoh/serializer/issues/134)
- Serialize to/from array? [\#133](https://github.com/schmittjoh/serializer/issues/133)
- @XmlRoot annotation no longer working [\#131](https://github.com/schmittjoh/serializer/issues/131)
- Skip an element based on a condition in a XmlList [\#121](https://github.com/schmittjoh/serializer/issues/121)

**Merged pull requests:**

- No CData [\#187](https://github.com/schmittjoh/serializer/pull/187) ([mvrhov](https://github.com/mvrhov))
- composer is preinstalled on travis [\#185](https://github.com/schmittjoh/serializer/pull/185) ([lsmith77](https://github.com/lsmith77))
- \[WIP\] added support for PHPCR [\#184](https://github.com/schmittjoh/serializer/pull/184) ([lsmith77](https://github.com/lsmith77))
- Metadata filename convention added to yml/xml references [\#172](https://github.com/schmittjoh/serializer/pull/172) ([rodrigodiez](https://github.com/rodrigodiez))
- Fix inline bug with empty child [\#165](https://github.com/schmittjoh/serializer/pull/165) ([adrienbrault](https://github.com/adrienbrault))
- Add virtual properties yaml example [\#163](https://github.com/schmittjoh/serializer/pull/163) ([adrienbrault](https://github.com/adrienbrault))
- Allow deserialization to constructed objects [\#160](https://github.com/schmittjoh/serializer/pull/160) ([eugene-dounar](https://github.com/eugene-dounar))
- Fix DoctrineDriverTest random failures [\#155](https://github.com/schmittjoh/serializer/pull/155) ([eugene-dounar](https://github.com/eugene-dounar))
- Fix XML null DateTime deserialization [\#154](https://github.com/schmittjoh/serializer/pull/154) ([eugene-dounar](https://github.com/eugene-dounar))
- Update doctrine/orm dev dependency [\#153](https://github.com/schmittjoh/serializer/pull/153) ([eugene-dounar](https://github.com/eugene-dounar))
- composer install --dev fails [\#152](https://github.com/schmittjoh/serializer/pull/152) ([eugene-dounar](https://github.com/eugene-dounar))
- Update annotations.rst [\#146](https://github.com/schmittjoh/serializer/pull/146) ([chrisjohnson00](https://github.com/chrisjohnson00))
- Add Doctrine\ODM\PHPCR\ChildrenCollection to ArrayCollectionHandler [\#143](https://github.com/schmittjoh/serializer/pull/143) ([hacfi](https://github.com/hacfi))
- xml\_attribute\_map fix for the yaml driver [\#142](https://github.com/schmittjoh/serializer/pull/142) ([mvanmeerbeck](https://github.com/mvanmeerbeck))
- fix wrong quote in used in docs [\#130](https://github.com/schmittjoh/serializer/pull/130) ([jaapio](https://github.com/jaapio))
- Support PropelCollection serialization [\#81](https://github.com/schmittjoh/serializer/pull/81) ([zebraf1](https://github.com/zebraf1))

## [0.13.0](https://github.com/schmittjoh/serializer/tree/0.13.0) (2013-07-29)

**Closed issues:**

- Documentation on Exclusion Strategies has an error [\#122](https://github.com/schmittjoh/serializer/issues/122)
- How access to the current serializing group in a subscriber ? [\#99](https://github.com/schmittjoh/serializer/issues/99)
- DoctrineProxySubscriber not found [\#93](https://github.com/schmittjoh/serializer/issues/93)
- Namespaces at root level [\#86](https://github.com/schmittjoh/serializer/issues/86)
- Issues when requesting JSON or XML using Doctrine MongoDB ODM [\#85](https://github.com/schmittjoh/serializer/issues/85)
- addGlobalIgnoredName not working [\#78](https://github.com/schmittjoh/serializer/issues/78)
- serialize\_null configuration [\#77](https://github.com/schmittjoh/serializer/issues/77)
- Add json prefix to prevent script tag csrf attack [\#76](https://github.com/schmittjoh/serializer/issues/76)
- Add support for replacing serialization object inside events [\#74](https://github.com/schmittjoh/serializer/issues/74)
- Next stable version? [\#64](https://github.com/schmittjoh/serializer/issues/64)
- Deserialize with object refs [\#62](https://github.com/schmittjoh/serializer/issues/62)

**Merged pull requests:**

- Document the handler $context argument [\#116](https://github.com/schmittjoh/serializer/pull/116) ([adrienbrault](https://github.com/adrienbrault))
- Document the SubscribingHandlerInterface a bit [\#115](https://github.com/schmittjoh/serializer/pull/115) ([adrienbrault](https://github.com/adrienbrault))
- Add getter for the xml serialization visitor defaultRootName property [\#114](https://github.com/schmittjoh/serializer/pull/114) ([adrienbrault](https://github.com/adrienbrault))
- Add Serializer::getMetadataFactory [\#113](https://github.com/schmittjoh/serializer/pull/113) ([adrienbrault](https://github.com/adrienbrault))
- Accessor order [\#108](https://github.com/schmittjoh/serializer/pull/108) ([jaapio](https://github.com/jaapio))
- Added xmlns:xsi namespace and fixed tests [\#107](https://github.com/schmittjoh/serializer/pull/107) ([josser](https://github.com/josser))
- \[Doc\] Fixed typo in event\_system [\#106](https://github.com/schmittjoh/serializer/pull/106) ([lyrixx](https://github.com/lyrixx))
- Fix discriminator map search in ClassMetadata [\#97](https://github.com/schmittjoh/serializer/pull/97) ([xanido](https://github.com/xanido))
- Use the AnnotationReader interface in the SerializerBuilder, instead of the implemented AnnotationReader itself [\#82](https://github.com/schmittjoh/serializer/pull/82) ([HarmenM](https://github.com/HarmenM))
- Remove useless YamlSerializationVisitor::prepare method [\#75](https://github.com/schmittjoh/serializer/pull/75) ([adrienbrault](https://github.com/adrienbrault))
- Add the PRE\_DESERIALIZE event to the Events class [\#73](https://github.com/schmittjoh/serializer/pull/73) ([adrienbrault](https://github.com/adrienbrault))
- Improve serialization example [\#71](https://github.com/schmittjoh/serializer/pull/71) ([tvlooy](https://github.com/tvlooy))
- Max depth strategy [\#4](https://github.com/schmittjoh/serializer/pull/4) ([adrienbrault](https://github.com/adrienbrault))

## [0.12.0](https://github.com/schmittjoh/serializer/tree/0.12.0) (2013-03-28)

**Closed issues:**

- Serialization profile/definition builder [\#68](https://github.com/schmittjoh/serializer/issues/68)
- I want to configure the default exclution policy [\#65](https://github.com/schmittjoh/serializer/issues/65)
- Mulit type property mapping [\#56](https://github.com/schmittjoh/serializer/issues/56)
- AccessType\("public\_method"\): Setters ignored when deserializing to non-standard XML properties [\#53](https://github.com/schmittjoh/serializer/issues/53)
- Adding @Accessor with custom getter causes LogicException if Doctrine ManyToOneEntity [\#52](https://github.com/schmittjoh/serializer/issues/52)
- Handler callback's does not get passed context [\#49](https://github.com/schmittjoh/serializer/issues/49)
- PostSerialize callback causes data loss [\#46](https://github.com/schmittjoh/serializer/issues/46)
- Empty Objects get serialized as "array\(\)" [\#43](https://github.com/schmittjoh/serializer/issues/43)
- Exclusion Policies aren't properly applied when "serializeNull" is "true" [\#42](https://github.com/schmittjoh/serializer/issues/42)
- Accessor annotation ignored [\#40](https://github.com/schmittjoh/serializer/issues/40)
- Support for multiple exclusion strategies [\#39](https://github.com/schmittjoh/serializer/issues/39)
- srholt123@yahoo.com [\#35](https://github.com/schmittjoh/serializer/issues/35)
- Could you tag a stable version? [\#34](https://github.com/schmittjoh/serializer/issues/34)
- Default conversion of camelCase to underscores is counterintuitive [\#33](https://github.com/schmittjoh/serializer/issues/33)
- Define the xml root when deserializing [\#18](https://github.com/schmittjoh/serializer/issues/18)

**Merged pull requests:**

- \[Annotation\] Added the ability to set the type when using @VirtualProperty [\#69](https://github.com/schmittjoh/serializer/pull/69) ([pylebecq](https://github.com/pylebecq))
- Added documentation for the @VirtualProperty annotation [\#67](https://github.com/schmittjoh/serializer/pull/67) ([pylebecq](https://github.com/pylebecq))
- Metadata stack tests [\#57](https://github.com/schmittjoh/serializer/pull/57) ([adrienbrault](https://github.com/adrienbrault))
- Adding context to twig extension [\#55](https://github.com/schmittjoh/serializer/pull/55) ([smurfy](https://github.com/smurfy))
- Allow deserialization of polymorphic classes by class without specifying the type [\#48](https://github.com/schmittjoh/serializer/pull/48) ([gordalina](https://github.com/gordalina))
- Moves all state to dedicated context class [\#47](https://github.com/schmittjoh/serializer/pull/47) ([schmittjoh](https://github.com/schmittjoh))
- Add PropertyNamingStrategy [\#37](https://github.com/schmittjoh/serializer/pull/37) ([passkey1510](https://github.com/passkey1510))
- The NavigatorContext now holds a metadata stack [\#28](https://github.com/schmittjoh/serializer/pull/28) ([adrienbrault](https://github.com/adrienbrault))

## [0.11.0](https://github.com/schmittjoh/serializer/tree/0.11.0) (2013-01-29)

**Closed issues:**

- Hooking into metadata directly... [\#17](https://github.com/schmittjoh/serializer/issues/17)
- Serializing null values [\#14](https://github.com/schmittjoh/serializer/issues/14)
- Strange caching-error [\#13](https://github.com/schmittjoh/serializer/issues/13)
- handling of plain array [\#10](https://github.com/schmittjoh/serializer/issues/10)
- Unsupported format doesn't throw exception anymore [\#8](https://github.com/schmittjoh/serializer/issues/8)

**Merged pull requests:**

- Fix typo [\#32](https://github.com/schmittjoh/serializer/pull/32) ([inanimatt](https://github.com/inanimatt))
- Fixed the serialization of pluralized form errors [\#31](https://github.com/schmittjoh/serializer/pull/31) ([stof](https://github.com/stof))
- Extract json specific logic from GenericSerializationVisitor [\#29](https://github.com/schmittjoh/serializer/pull/29) ([adrienbrault](https://github.com/adrienbrault))
- \[Serializer\] Misc cleanup [\#27](https://github.com/schmittjoh/serializer/pull/27) ([vicb](https://github.com/vicb))
- \[Builder\] Add ability to include if metadata [\#25](https://github.com/schmittjoh/serializer/pull/25) ([vicb](https://github.com/vicb))
- Fix DateTimeZone issue when using the DateTime type [\#23](https://github.com/schmittjoh/serializer/pull/23) ([colinmorelli](https://github.com/colinmorelli))
- Wrong exception message for parsing datetime [\#21](https://github.com/schmittjoh/serializer/pull/21) ([nickelc](https://github.com/nickelc))
- Fixed typo in doc/reference/annotations.rst [\#16](https://github.com/schmittjoh/serializer/pull/16) ([iambrosi](https://github.com/iambrosi))
- Typecast when serializing primitive types [\#15](https://github.com/schmittjoh/serializer/pull/15) ([baldurrensch](https://github.com/baldurrensch))
- add check and helpful exception message on inconsistent type situation [\#12](https://github.com/schmittjoh/serializer/pull/12) ([dbu](https://github.com/dbu))
- Dispatch pre-serialization event before handling data to have ability change type in listener [\#7](https://github.com/schmittjoh/serializer/pull/7) ([megazoll](https://github.com/megazoll))
- Fix tests running in different environments [\#6](https://github.com/schmittjoh/serializer/pull/6) ([megazoll](https://github.com/megazoll))
- Add DateInterval serialization to DateHandler formerly DateTimeHandler [\#5](https://github.com/schmittjoh/serializer/pull/5) ([rpg600](https://github.com/rpg600))
- WIP Navigator context [\#3](https://github.com/schmittjoh/serializer/pull/3) ([adrienbrault](https://github.com/adrienbrault))
- Update src/JMS/Serializer/Construction/DoctrineObjectConstructor.php [\#2](https://github.com/schmittjoh/serializer/pull/2) ([robocoder](https://github.com/robocoder))
- Filter out non-identifiers from $data before calling find\(\) [\#1](https://github.com/schmittjoh/serializer/pull/1) ([robocoder](https://github.com/robocoder))



\* *This Change Log was automatically generated by [github_changelog_generator](https://github.com/skywinder/Github-Changelog-Generator)*
