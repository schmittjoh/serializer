# Change Log

## [1.5.0-RC1](https://github.com/schmittjoh/serializer/tree/1.5.0-RC1) (2017-01-19)
**Implemented enhancements:**

- added support for xml-attributes as discriminators [\#692](https://github.com/schmittjoh/serializer/pull/692) ([twtinteractive](https://github.com/twtinteractive))
- Prevent doctrine proxy loading for virtual types [\#684](https://github.com/schmittjoh/serializer/pull/684) ([goetas](https://github.com/goetas))
- Implemented dynamic exclusion using symfony expression language [\#673](https://github.com/schmittjoh/serializer/pull/673) ([goetas](https://github.com/goetas))

**Fixed bugs:**

- Deserializing XMLList with Namespaces not \(always\) working as intended [\#697](https://github.com/schmittjoh/serializer/pull/697) ([goetas](https://github.com/goetas))

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

**Merged pull requests:**

- Revert "Default `$serializeNull` to false" [\#630](https://github.com/schmittjoh/serializer/pull/630) ([goetas](https://github.com/goetas))

## [1.3.0](https://github.com/schmittjoh/serializer/tree/1.3.0) (2016-08-17)
**Closed issues:**

- problems with xml namespaces after update [\#621](https://github.com/schmittjoh/serializer/issues/621)
- Trying to decorate a member to ArrayCollection but gets an error when deserilizing because composer didn't download the class from doctrine. [\#596](https://github.com/schmittjoh/serializer/issues/596)
- Missing doctrine/common requirement ? [\#517](https://github.com/schmittjoh/serializer/issues/517)
- PHP Fatal error: Using $this when not in object context in JMS/Serializer/Serializer.php on line 99 [\#441](https://github.com/schmittjoh/serializer/issues/441)
- Exclude annotation not preventing attempt to find public methods when using AccessType [\#367](https://github.com/schmittjoh/serializer/issues/367)
- serializer.pre\_serialize event only thrown on objects/classes [\#337](https://github.com/schmittjoh/serializer/issues/337)
- Installing through composer gets "Segmentation fault" [\#308](https://github.com/schmittjoh/serializer/issues/308)
- Erroneous data format for unserializing... [\#283](https://github.com/schmittjoh/serializer/issues/283)
- DoctrineObjectConstructor should skip empty identifier field [\#193](https://github.com/schmittjoh/serializer/issues/193)

**Merged pull requests:**

- Added public `hasData` function to check if a data key already have been added. [\#625](https://github.com/schmittjoh/serializer/pull/625) ([goetas](https://github.com/goetas))
- $context is not used [\#622](https://github.com/schmittjoh/serializer/pull/622) ([olvlvl](https://github.com/olvlvl))
- Fix Doctrine PHPCR ODM 2.0 compatibility [\#605](https://github.com/schmittjoh/serializer/pull/605) ([wouterj](https://github.com/wouterj))
- Introducing NormalizerInterface [\#592](https://github.com/schmittjoh/serializer/pull/592) ([alcalyn](https://github.com/alcalyn))
- Fixed type-hinting [\#586](https://github.com/schmittjoh/serializer/pull/586) ([jgendera](https://github.com/jgendera))
- Fix multiple handler callbacks in YamlDriver [\#515](https://github.com/schmittjoh/serializer/pull/515) ([mpajunen](https://github.com/mpajunen))
- Fixed minor typos [\#364](https://github.com/schmittjoh/serializer/pull/364) ([sdaoudi](https://github.com/sdaoudi))
- Default `$serializeNull` to false [\#317](https://github.com/schmittjoh/serializer/pull/317) ([steveYeah](https://github.com/steveYeah))
- Missing attribute 'xml-value' in XML Reference [\#269](https://github.com/schmittjoh/serializer/pull/269) ([holtkamp](https://github.com/holtkamp))
- Removed unnecessary use statement [\#262](https://github.com/schmittjoh/serializer/pull/262) ([dunglas](https://github.com/dunglas))

## [1.2.0](https://github.com/schmittjoh/serializer/tree/1.2.0) (2016-08-03)
**Implemented enhancements:**

- Issue543 - Adding DateTimeImmutable support [\#635](https://github.com/schmittjoh/serializer/pull/635) ([toby-griffiths](https://github.com/toby-griffiths))

**Fixed bugs:**

- Fix xml-attribute-map for the xml driver [\#595](https://github.com/schmittjoh/serializer/pull/595) ([romantomchak](https://github.com/romantomchak))
- Fix warning array\_key\_exists in deserialization. [\#398](https://github.com/schmittjoh/serializer/pull/398) ([leonnleite](https://github.com/leonnleite))
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
- Discriminator Groups [\#579](https://github.com/schmittjoh/serializer/pull/579) ([maennchen](https://github.com/maennchen))
- Fixed test suite on master [\#578](https://github.com/schmittjoh/serializer/pull/578) ([goetas](https://github.com/goetas))
- Fix for a broken test: a missing \(incorrectly positioned\) argument [\#577](https://github.com/schmittjoh/serializer/pull/577) ([zerkms](https://github.com/zerkms))
- Add extra test for handling child elements [\#569](https://github.com/schmittjoh/serializer/pull/569) ([tarjei](https://github.com/tarjei))
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
- Virtual property documentation xml & yaml [\#100](https://github.com/schmittjoh/serializer/issues/100)

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

## [0.14.0](https://github.com/schmittjoh/serializer/tree/0.14.0) (2013-12-04)
**Implemented enhancements:**

- Can now override groups on specific paths of the graph [\#170](https://github.com/schmittjoh/serializer/pull/170) ([adrienbrault](https://github.com/adrienbrault))

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
- Support PropelCollection serialization [\#81](https://github.com/schmittjoh/serializer/pull/81) ([zebraf1](https://github.com/zebraf1))
- Adds XML namespaces support [\#58](https://github.com/schmittjoh/serializer/pull/58) ([ajgarlag](https://github.com/ajgarlag))

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

- fix wrong quote in used in docs [\#130](https://github.com/schmittjoh/serializer/pull/130) ([jaapio](https://github.com/jaapio))
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