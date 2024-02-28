# Changelog

New versions can be found on the [realeases page](https://github.com/schmittjoh/serializer/releases)

## [3.15.0](https://github.com/schmittjoh/serializer/tree/3.15.0) (2021-10-14)

**Merged pull requests:**

- allow the groups annotation to use "groups" as parameter name [\#1351](https://github.com/schmittjoh/serializer/pull/1351) ([goetas](https://github.com/goetas))
- Allow stable PHPStan PHPDoc Parser [\#1346](https://github.com/schmittjoh/serializer/pull/1346) ([mbabker](https://github.com/mbabker))
- Symfony 6 and DBAL 3 compat [\#1345](https://github.com/schmittjoh/serializer/pull/1345) ([mbabker](https://github.com/mbabker))
- Proposed fix for serializing custom DateTimeInterface implementations [\#1344](https://github.com/schmittjoh/serializer/pull/1344) ([andrei-dascalu](https://github.com/andrei-dascalu))
- Allow to add prefix to twig helpers [\#1341](https://github.com/schmittjoh/serializer/pull/1341) ([goetas](https://github.com/goetas))
- Fix phpstan return type [\#1329](https://github.com/schmittjoh/serializer/pull/1329) ([dgafka](https://github.com/dgafka))

## [3.14.0](https://github.com/schmittjoh/serializer/tree/3.14.0) (2021-08-06)

**Merged pull requests:**

- Avoid duplicate/invalid definitions when loading the php attributes using the annotation driver [\#1340](https://github.com/schmittjoh/serializer/pull/1340) ([goetas](https://github.com/goetas))

## [3.14.0-rc2](https://github.com/schmittjoh/serializer/tree/3.14.0-rc2) (2021-08-06)

**Merged pull requests:**

- run php8 ci on high and low deps [\#1339](https://github.com/schmittjoh/serializer/pull/1339) ([goetas](https://github.com/goetas))
- php8 attributes are enabled by default on php8 or higher [\#1338](https://github.com/schmittjoh/serializer/pull/1338) ([goetas](https://github.com/goetas))
- Allow positional php8 attributes [\#1337](https://github.com/schmittjoh/serializer/pull/1337) ([goetas](https://github.com/goetas))
- Drop Travis [\#1306](https://github.com/schmittjoh/serializer/pull/1306) ([simPod](https://github.com/simPod))

## [3.14.0-rc1](https://github.com/schmittjoh/serializer/tree/3.14.0-rc1) (2021-08-01)

**Merged pull requests:**

- Add PHP attributes support [\#1332](https://github.com/schmittjoh/serializer/pull/1332) ([goetas](https://github.com/goetas))
- Deprecate `@ReadOnly` annotation in favor of `@ReadOnlyProperty` [\#1333](https://github.com/schmittjoh/serializer/pull/1333) ([mbabker](https://github.com/mbabker))

## [3.13.0](https://github.com/schmittjoh/serializer/tree/3.13.0) (2021-07-05)

**Merged pull requests:**

- Use FilesystemAdapter when possible to fix compatibility with doctrine/cache 2 [\#1328](https://github.com/schmittjoh/serializer/pull/1328) ([rasmustnilsson](https://github.com/rasmustnilsson))
- Use PsrCachedReader and drop doctrine/cache [\#1327](https://github.com/schmittjoh/serializer/pull/1327) ([simPod](https://github.com/simPod))
- Check data can be casted before actual casting [\#1317](https://github.com/schmittjoh/serializer/pull/1317) ([scaytrase](https://github.com/scaytrase))
- Add methods for data\_collector [\#1316](https://github.com/schmittjoh/serializer/pull/1316) ([gam6itko](https://github.com/gam6itko))
- fix iterable::class that does not exist [\#1315](https://github.com/schmittjoh/serializer/pull/1315) ([Tobion](https://github.com/Tobion))
- useful error when data is not an object [\#1313](https://github.com/schmittjoh/serializer/pull/1313) ([dbu](https://github.com/dbu))
- Fix callback-method setup using XmlDriver [\#1310](https://github.com/schmittjoh/serializer/pull/1310) ([curzio-della-santa](https://github.com/curzio-della-santa))

## [3.12.3](https://github.com/schmittjoh/serializer/tree/3.12.3) (2021-04-25)

**Merged pull requests:**

- \[docs\] Add documentation to deserialize on existing objects [\#1308](https://github.com/schmittjoh/serializer/pull/1308) ([gam6itko](https://github.com/gam6itko))
- Allow phpstan/phpdoc-parser v0.5 [\#1307](https://github.com/schmittjoh/serializer/pull/1307) ([simPod](https://github.com/simPod))

## [3.12.2](https://github.com/schmittjoh/serializer/tree/3.12.2) (2021-03-23)

**Fixed bugs:**

- `Undefined offset: 0` when using `@var null|string` instead of `@var string|null` [\#1301](https://github.com/schmittjoh/serializer/pull/1301) ([ruudk](https://github.com/ruudk))

**Merged pull requests:**

- move around some doc block classes [\#1304](https://github.com/schmittjoh/serializer/pull/1304) ([goetas](https://github.com/goetas))

## [3.12.1](https://github.com/schmittjoh/serializer/tree/3.12.1) (2021-03-21)

**Fixed bugs:**

- Fix for issue \#1286: loading fails when deserializing XML [\#1289](https://github.com/schmittjoh/serializer/pull/1289) ([jviersel-ipronto](https://github.com/jviersel-ipronto))
- Fix DocBlockTypeResolver crash on PHP 7.3 and less [\#1288](https://github.com/schmittjoh/serializer/pull/1288) ([simPod](https://github.com/simPod))
- Doctrine `json` field type can contain not only an array [\#1295](https://github.com/schmittjoh/serializer/pull/1295) ([gam6itko](https://github.com/gam6itko))

**Merged pull requests:**

- add missing CustomPropertyOrderingStrategyTest [\#1296](https://github.com/schmittjoh/serializer/pull/1296) ([gam6itko](https://github.com/gam6itko))
- fix \#314 [\#1293](https://github.com/schmittjoh/serializer/pull/1293) ([gam6itko](https://github.com/gam6itko))
- Show all toctree on index page [\#1292](https://github.com/schmittjoh/serializer/pull/1292) ([gam6itko](https://github.com/gam6itko))

# Changelog

## [3.12.0](https://github.com/schmittjoh/serializer/tree/3.12.0) (2021-03-04)

**Fixed bugs:**

- Remove from the serialization groups if no match [\#1291](https://github.com/schmittjoh/serializer/pull/1291) ([goetas](https://github.com/goetas))

**Merged pull requests:**

- \[DOCS\] Add 'Deserialization Exclusion Strategy with Groups' topic [\#1287](https://github.com/schmittjoh/serializer/pull/1287) ([gam6itko](https://github.com/gam6itko))
- Add ascii\_string, dateinterval, and json to doctrine type mapping [\#1281](https://github.com/schmittjoh/serializer/pull/1281) ([dontub](https://github.com/dontub))
- Cleanup [\#1278](https://github.com/schmittjoh/serializer/pull/1278) ([simPod](https://github.com/simPod))
- Drop coverage badge [\#1277](https://github.com/schmittjoh/serializer/pull/1277) ([simPod](https://github.com/simPod))
- Introduce PHPStan [\#1276](https://github.com/schmittjoh/serializer/pull/1276) ([simPod](https://github.com/simPod))
- Replace Scrutinizer with GA [\#1275](https://github.com/schmittjoh/serializer/pull/1275) ([simPod](https://github.com/simPod))
- Add throws tag [\#1273](https://github.com/schmittjoh/serializer/pull/1273) ([VincentLanglet](https://github.com/VincentLanglet))

## [3.11.0](https://github.com/schmittjoh/serializer/tree/3.11.0) (2020-12-29)

**Implemented enhancements:**

- Allow installing and build on PHP 8 [\#1267](https://github.com/schmittjoh/serializer/pull/1267) ([sanmai](https://github.com/sanmai))
- Use phpstan/phpdoc-parser to retrieve additional type information from PhpDoc [\#1261](https://github.com/schmittjoh/serializer/pull/1261) ([Namoshek](https://github.com/Namoshek))
- DoctrineObjectConstructor Using array\_key\_exists\(\) on objects is deprecated in php7.4 [\#1253](https://github.com/schmittjoh/serializer/pull/1253) ([gam6itko](https://github.com/gam6itko))
- Add Composer cache for v2 on Travis CI [\#1266](https://github.com/schmittjoh/serializer/pull/1266) ([sanmai](https://github.com/sanmai))
- Allow interfaces for DocBlock [\#1256](https://github.com/schmittjoh/serializer/pull/1256) ([marein](https://github.com/marein))
- Allow interfaces for typed properties [\#1254](https://github.com/schmittjoh/serializer/pull/1254) ([marein](https://github.com/marein))

## [3.10.0](https://github.com/schmittjoh/serializer/tree/3.10.0) (2020-10-29)

**Implemented enhancements:**

- Allow null to be visited if is a root object [\#1250](https://github.com/schmittjoh/serializer/pull/1250) ([goetas](https://github.com/goetas))
- Resolve collections from DocBlock [\#1214](https://github.com/schmittjoh/serializer/pull/1214) ([dgafka](https://github.com/dgafka))

**Merged pull requests:**

- Bump CS [\#1249](https://github.com/schmittjoh/serializer/pull/1249) ([simPod](https://github.com/simPod))
- Removed redundant property initialization [\#1232](https://github.com/schmittjoh/serializer/pull/1232) ([xepozz](https://github.com/xepozz))


## [3.9.0](https://github.com/schmittjoh/serializer/tree/3.9.0) (2020-08-26)

**Implemented enhancements:**

- Add support for skippable \(de\)serialization handlers [\#1238](https://github.com/schmittjoh/serializer/pull/1238) ([bobvandevijver](https://github.com/bobvandevijver))
- added support for milliseconds in DateInterval deserialization [\#1234](https://github.com/schmittjoh/serializer/pull/1234) ([ivoba](https://github.com/ivoba))

**Fixed bugs:**

- Do not load entities when deserializing if their identifier is not ex… [\#1247](https://github.com/schmittjoh/serializer/pull/1247) ([goetas](https://github.com/goetas))
- Do not use excluded fields when fetching entities [\#1246](https://github.com/schmittjoh/serializer/pull/1246) ([goetas](https://github.com/goetas))
- Ensure accessors are cached per property when using reflection [\#1237](https://github.com/schmittjoh/serializer/pull/1237) ([goetas](https://github.com/goetas))

**Closed issues:**

- Annotation cache does not honor naming strategy [\#1244](https://github.com/schmittjoh/serializer/issues/1244)
- Authorization Bypass Vulnerability - v1.14.1 [\#1242](https://github.com/schmittjoh/serializer/issues/1242)
- @SkipWhenEmpty and @Exclude combination leads to unexpected behavior [\#1240](https://github.com/schmittjoh/serializer/issues/1240)
- How to pass MetadataFactory::create\(\) into SerializationVisitorInterface::startVisitingObject\(\)? [\#1226](https://github.com/schmittjoh/serializer/issues/1226)
- Custom type in array key is not respected when serializing to JSON [\#1223](https://github.com/schmittjoh/serializer/issues/1223)
- xml:id or xml:lang attributes handling [\#1221](https://github.com/schmittjoh/serializer/issues/1221)
- Accessing static property as non static [\#1156](https://github.com/schmittjoh/serializer/issues/1156)
- AbstractVisitor::getElementType\(\) must be of the type array or null, string returned [\#1027](https://github.com/schmittjoh/serializer/issues/1027)

**Merged pull requests:**

- remove missing deprecated removal on hasData [\#1245](https://github.com/schmittjoh/serializer/pull/1245) ([rflavien](https://github.com/rflavien))
- Change return type of SerializerBuilder::build\(\) to Serializer [\#1241](https://github.com/schmittjoh/serializer/pull/1241) ([icanhazstring](https://github.com/icanhazstring))
- docs: add note about array key type being ignored when serializing [\#1235](https://github.com/schmittjoh/serializer/pull/1235) ([eduardoweiland](https://github.com/eduardoweiland))
- Sort packages in composer.json [\#1228](https://github.com/schmittjoh/serializer/pull/1228) ([simPod](https://github.com/simPod))
- fix xml embeddable data getReference for DoctrineObjectConstructor [\#1224](https://github.com/schmittjoh/serializer/pull/1224) ([gam6itko](https://github.com/gam6itko))
- fixed exception for strict\_types [\#1222](https://github.com/schmittjoh/serializer/pull/1222) ([ivoba](https://github.com/ivoba))

## [3.8.0](https://github.com/schmittjoh/serializer/tree/3.8.0) (2020-06-28)

**Implemented enhancements:**

- Use doctrine/lexer instead of hoa/compiler [\#1212](https://github.com/schmittjoh/serializer/pull/1212) ([goetas](https://github.com/goetas))

**Fixed bugs:**

- Consider exclude rules on parents if defined [\#1206](https://github.com/schmittjoh/serializer/pull/1206) ([goetas](https://github.com/goetas))

**Closed issues:**

- Serializer Group [\#1213](https://github.com/schmittjoh/serializer/issues/1213)
- Notice: Accessing static property Proxies\\_\_CG\_\_\examplemodel\inherit\Customers::$lazyPropertiesNames as non static [\#1209](https://github.com/schmittjoh/serializer/issues/1209)
- Unserialization failure after upgrading to 3.7.0 \(`excludeIf` related?\) [\#1207](https://github.com/schmittjoh/serializer/issues/1207)
- \[RFC\] Removing abandoned hoa from serializer [\#1182](https://github.com/schmittjoh/serializer/issues/1182)
- hoa/protocol package conflicts with laravel helper [\#1154](https://github.com/schmittjoh/serializer/issues/1154)

**Merged pull requests:**

- Remove conflicts to hoa packages [\#1216](https://github.com/schmittjoh/serializer/pull/1216) ([alexander-schranz](https://github.com/alexander-schranz))
- Test also agains twig 3 [\#1215](https://github.com/schmittjoh/serializer/pull/1215) ([alexander-schranz](https://github.com/alexander-schranz))
- Allow doctrine/persistence v2/v3 [\#1210](https://github.com/schmittjoh/serializer/pull/1210) ([goetas](https://github.com/goetas))
- Fix deprecated assertFileNotExist [\#1197](https://github.com/schmittjoh/serializer/pull/1197) ([mpoiriert](https://github.com/mpoiriert))

## [3.7.0](https://github.com/schmittjoh/serializer/tree/3.7.0) (2020-05-23)

**Implemented enhancements:**

- Allow deserialization of typehinted DateTimeInterface to DateTime class [\#1193](https://github.com/schmittjoh/serializer/pull/1193) ([goetas](https://github.com/goetas))
- Infer types from PHP 7.4 type declarations [\#1192](https://github.com/schmittjoh/serializer/pull/1192) ([goetas](https://github.com/goetas))
- Support conditional exclude for classes [\#1099](https://github.com/schmittjoh/serializer/pull/1099) ([arneee](https://github.com/arneee))

**Fixed bugs:**

- Exclude if at class level are not merge [\#1203](https://github.com/schmittjoh/serializer/issues/1203)
- Class level expression exclusion strategy should work with hierarchies   [\#1204](https://github.com/schmittjoh/serializer/pull/1204) ([goetas](https://github.com/goetas))

**Closed issues:**

- Specify Type as nullable? [\#1191](https://github.com/schmittjoh/serializer/issues/1191)
- Does someone know how to use phpdoc with serializer? [\#1185](https://github.com/schmittjoh/serializer/issues/1185)
- Serializer doesn't keep types but convert them if not it can [\#1181](https://github.com/schmittjoh/serializer/issues/1181)
- ConditionalExpose/Exclude annotation does not work on class level [\#1098](https://github.com/schmittjoh/serializer/issues/1098)

**Merged pull requests:**

- \[Docs\] Improve documentation on dynamic exclusion strategy [\#1188](https://github.com/schmittjoh/serializer/pull/1188) ([arneee](https://github.com/arneee))
- Fix Support conditional exclude for classes [\#1187](https://github.com/schmittjoh/serializer/pull/1187) ([arneee](https://github.com/arneee))
- Fix travis tests [\#1183](https://github.com/schmittjoh/serializer/pull/1183) ([peter279k](https://github.com/peter279k))
- Replace "Exclude" by "Expose" [\#1180](https://github.com/schmittjoh/serializer/pull/1180) ([kpn13](https://github.com/kpn13))
- add .gitattributes [\#1177](https://github.com/schmittjoh/serializer/pull/1177) ([Tobion](https://github.com/Tobion))

## [3.6.0](https://github.com/schmittjoh/serializer/tree/3.6.0) (2020-03-21)

**Implemented enhancements:**

- DateTime parsed invalid date [\#1152](https://github.com/schmittjoh/serializer/issues/1152)
- do not hide Exceptions from custom handlers but correctly handle null [\#1169](https://github.com/schmittjoh/serializer/pull/1169) ([Hikariii](https://github.com/Hikariii))

**Fixed bugs:**

- Handle discriminator groups [\#1175](https://github.com/schmittjoh/serializer/pull/1175) ([goetas](https://github.com/goetas))

**Closed issues:**

- thrown Exceptions are hidden when serializing complex objects with a handler [\#1168](https://github.com/schmittjoh/serializer/issues/1168)

**Merged pull requests:**

- test serializing entity that uses Discriminator and extends some base… [\#1174](https://github.com/schmittjoh/serializer/pull/1174) ([FrKevin](https://github.com/FrKevin))
- Handle ObjectConstructor returning NULL [\#1172](https://github.com/schmittjoh/serializer/pull/1172) ([jankramer](https://github.com/jankramer))
- test symfony translator contract [\#1171](https://github.com/schmittjoh/serializer/pull/1171) ([goetas](https://github.com/goetas))

## [3.5.0](https://github.com/schmittjoh/serializer/tree/3.5.0) (2020-02-22)

**Implemented enhancements:**

- Improved return type for fluent methods in Context [\#1162](https://github.com/schmittjoh/serializer/pull/1162) ([wouterj](https://github.com/wouterj))
- Handle array format for dateHandler [\#1108](https://github.com/schmittjoh/serializer/pull/1108) ([VincentLanglet](https://github.com/VincentLanglet))

**Fixed bugs:**

- Make sure serialzation context is immutable [\#1159](https://github.com/schmittjoh/serializer/pull/1159) ([goetas](https://github.com/goetas))

**Merged pull requests:**

- Allow for newer PHPUnit [\#1166](https://github.com/schmittjoh/serializer/pull/1166) ([sanmai](https://github.com/sanmai))
- \[Docs\] Explain recursion in FileLocator [\#1155](https://github.com/schmittjoh/serializer/pull/1155) ([ruudk](https://github.com/ruudk))
- Changed CI environment to stable PHP 7.4 [\#1153](https://github.com/schmittjoh/serializer/pull/1153) ([grogy](https://github.com/grogy))

## [1.14.1](https://github.com/schmittjoh/serializer/tree/1.14.1) (2020-02-22)

**Closed issues:**

- Virtual Property do not get serialized if getter name conflict with a class property [\#1164](https://github.com/schmittjoh/serializer/issues/1164)
- SerializationGraphNavigator not receiving correct serializeNull config during initialize [\#1158](https://github.com/schmittjoh/serializer/issues/1158)
- SerializationGraphNavigator  unaware of serializeNull change of context when altered in PreSerializeEvent [\#1157](https://github.com/schmittjoh/serializer/issues/1157)
- Memory leaks [\#1150](https://github.com/schmittjoh/serializer/issues/1150)
- Properties with @Groups annotations included in output when no SerializationContext given. [\#1149](https://github.com/schmittjoh/serializer/issues/1149)

**Merged pull requests:**

- PHP7.4 ternary operator deprecation [\#1163](https://github.com/schmittjoh/serializer/pull/1163) ([adhocore](https://github.com/adhocore))
- Test 1.x on PHP 7.3 on Travis; fix builds for PHP 5.5 [\#1119](https://github.com/schmittjoh/serializer/pull/1119) ([sanmai](https://github.com/sanmai))

## [3.4.0](https://github.com/schmittjoh/serializer/tree/3.4.0) (2019-12-14)

**Implemented enhancements:**

- Symfony 5.0 compatibility [\#1145](https://github.com/schmittjoh/serializer/pull/1145) ([goetas](https://github.com/goetas))
- Support new doctrine ODM proxy objects [\#1139](https://github.com/schmittjoh/serializer/pull/1139) ([notrix](https://github.com/notrix))
- Visitor interfaces in handlers [\#1129](https://github.com/schmittjoh/serializer/pull/1129) ([derzkiy](https://github.com/derzkiy))

**Closed issues:**

- \[Improvement\] Ability to define a global exclusion\_policy: ALL for all classes. [\#1144](https://github.com/schmittjoh/serializer/issues/1144)
- Embed JSON string without extra escape [\#1142](https://github.com/schmittjoh/serializer/issues/1142)
- Make possible to set ArrayCollectionHandler classes from outside [\#1131](https://github.com/schmittjoh/serializer/issues/1131)

**Merged pull requests:**

- Remove PHP 7.4 from `allow\_failures` matrix [\#1138](https://github.com/schmittjoh/serializer/pull/1138) ([carusogabriel](https://github.com/carusogabriel))
- Remove unnecessary cast [\#1133](https://github.com/schmittjoh/serializer/pull/1133) ([carusogabriel](https://github.com/carusogabriel))
- Fix PHPUnit deprecations [\#1123](https://github.com/schmittjoh/serializer/pull/1123) ([Majkl578](https://github.com/Majkl578))

## [3.3.0](https://github.com/schmittjoh/serializer/tree/3.3.0) (2019-09-20)

**Implemented enhancements:**

- Update major version that v2.x deprecation will be removed [\#1134](https://github.com/schmittjoh/serializer/pull/1134) ([carusogabriel](https://github.com/carusogabriel))
- Implement short expose syntax for XML as it is available for YAML [\#1127](https://github.com/schmittjoh/serializer/pull/1127) ([goetas](https://github.com/goetas))

**Fixed bugs:**

- Avoid implicit expose of a property instead of virtual-property  [\#1126](https://github.com/schmittjoh/serializer/pull/1126) ([goetas](https://github.com/goetas))

**Closed issues:**

- Accessing static property as non static [\#1122](https://github.com/schmittjoh/serializer/issues/1122)
- Travis builds on 1.x are failing [\#1120](https://github.com/schmittjoh/serializer/issues/1120)

**Merged pull requests:**

- Allow failures on php "7.4snapshot" \(waiting for stable symfony 4.4\) [\#1128](https://github.com/schmittjoh/serializer/pull/1128) ([goetas](https://github.com/goetas))

## [3.2.0](https://github.com/schmittjoh/serializer/tree/3.2.0) (2019-09-04)

**Fixed bugs:**

- PHP7.4: Deprecated warning - serializationContext.php on line 152 [\#1111](https://github.com/schmittjoh/serializer/issues/1111)

**Closed issues:**

- StaticPropertyMetadata first constructor argument not nullable [\#1116](https://github.com/schmittjoh/serializer/issues/1116)
- Add support for PSR-7 URIInterface objects [\#1115](https://github.com/schmittjoh/serializer/issues/1115)
- Upgraded 2.4 -\> 3.4 / Symfony 4.3.3 [\#1112](https://github.com/schmittjoh/serializer/issues/1112)
- Empty namespace [\#1087](https://github.com/schmittjoh/serializer/issues/1087)
- Format constants \(JSON, XML\) [\#1079](https://github.com/schmittjoh/serializer/issues/1079)
- @ExclusionPolicy\(policy="ALL"\) causes PHP notice message [\#1073](https://github.com/schmittjoh/serializer/issues/1073)

**Merged pull requests:**

- Explain once and for all the use of StaticPropertyMetadata [\#1118](https://github.com/schmittjoh/serializer/pull/1118) ([goetas](https://github.com/goetas))
- PHP 7.4 compatibility  [\#1113](https://github.com/schmittjoh/serializer/pull/1113) ([goetas](https://github.com/goetas))
- Fix typos in UPGRADING.md [\#1107](https://github.com/schmittjoh/serializer/pull/1107) ([jdreesen](https://github.com/jdreesen))
- Fix exclusion policy bug [\#1106](https://github.com/schmittjoh/serializer/pull/1106) ([spam312sn](https://github.com/spam312sn))
- Add Doctrine 2 immutable datetime types to field mapping. [\#1104](https://github.com/schmittjoh/serializer/pull/1104) ([Sonny812](https://github.com/Sonny812))

## [3.1.1](https://github.com/schmittjoh/serializer/tree/3.1.1) (2019-06-28)

**Fixed bugs:**

- Could not deserialize object if all properties have not type [\#1102](https://github.com/schmittjoh/serializer/issues/1102)
- Revert "Move type check when deserializing into the graph navigator" [\#1103](https://github.com/schmittjoh/serializer/pull/1103) ([goetas](https://github.com/goetas))

## [3.1.0](https://github.com/schmittjoh/serializer/tree/3.1.0) (2019-06-25)

**Implemented enhancements:**

- Add support for iterable and Iterator [\#1096](https://github.com/schmittjoh/serializer/pull/1096) ([simPod](https://github.com/simPod))
- Implement "empty" XML namespace handling [\#1095](https://github.com/schmittjoh/serializer/pull/1095) ([discordier](https://github.com/discordier))
- Move type check when deserializing into the graph navigator [\#1080](https://github.com/schmittjoh/serializer/pull/1080) ([goetas](https://github.com/goetas))
- Allow loading different YAML extensions [\#1078](https://github.com/schmittjoh/serializer/pull/1078) ([scaytrase](https://github.com/scaytrase))

**Fixed bugs:**

- Fix for failing doctrine object constructor on embeddable class [\#1031](https://github.com/schmittjoh/serializer/pull/1031) ([notrix](https://github.com/notrix))

**Closed issues:**

- Behavior serializeNull -\> not always honored in 2.\* \(but was in 1.\*\) [\#1101](https://github.com/schmittjoh/serializer/issues/1101)
- Support for iterable [\#1094](https://github.com/schmittjoh/serializer/issues/1094)
- Prevent deserialisation with missing required field [\#1090](https://github.com/schmittjoh/serializer/issues/1090)
- Allow using @XmlValue together with @Accessor/@AccessType [\#1083](https://github.com/schmittjoh/serializer/issues/1083)
- Support \*.yaml extension [\#1077](https://github.com/schmittjoh/serializer/issues/1077)
- Instructions for upgrading from addData in 1.x don't work [\#1030](https://github.com/schmittjoh/serializer/issues/1030)

**Merged pull requests:**

- Add psalm specific generic return type for deserialize [\#1091](https://github.com/schmittjoh/serializer/pull/1091) ([bdsl](https://github.com/bdsl))
- Fix: Typo [\#1084](https://github.com/schmittjoh/serializer/pull/1084) ([localheinz](https://github.com/localheinz))

## [3.0.1](https://github.com/schmittjoh/serializer/tree/3.0.1) (2019-04-23)

**Fixed bugs:**

- Do not throw exception when visiting null in custom handler [\#1076](https://github.com/schmittjoh/serializer/pull/1076) ([goetas](https://github.com/goetas))

## [3.0.0](https://github.com/schmittjoh/serializer/tree/3.0.0) (2019-04-23)

**Breaking changes:**

- Revert v2 nested groups and release 3.0 [\#1071](https://github.com/schmittjoh/serializer/pull/1071) ([goetas](https://github.com/goetas))

**Implemented enhancements:**

- use Twig 2.7 namespaces [\#1061](https://github.com/schmittjoh/serializer/pull/1061) ([IonBazan](https://github.com/IonBazan))

**Closed issues:**

- \[RFC\] revert \#946 and release new major  [\#1058](https://github.com/schmittjoh/serializer/issues/1058)

**Merged pull requests:**

- Fix Travis-CI scripts always passing [\#1075](https://github.com/schmittjoh/serializer/pull/1075) ([IonBazan](https://github.com/IonBazan))

## [1.14.0](https://github.com/schmittjoh/serializer/tree/1.14.0) (2019-04-17)

## [2.3.0](https://github.com/schmittjoh/serializer/tree/2.3.0) (2019-04-17)

**Implemented enhancements:**

- Expose and test GroupsExclusionStrategy::getGroupsFor\(\) [\#1069](https://github.com/schmittjoh/serializer/pull/1069) ([goetas](https://github.com/goetas))
- add options property to XmlDeserializationVisitorFactory and XmlDeserializationVisitor, propagate defined value from factory to simplexml\_load\_string call [\#1068](https://github.com/schmittjoh/serializer/pull/1068) ([kopeckyales](https://github.com/kopeckyales))

**Closed issues:**

- Override existing property with another [\#1067](https://github.com/schmittjoh/serializer/issues/1067)
- disabling cdata by default [\#1065](https://github.com/schmittjoh/serializer/issues/1065)
- unwrap child class instance [\#1064](https://github.com/schmittjoh/serializer/issues/1064)
- Make JsonDeserializationVisitor extendable [\#1055](https://github.com/schmittjoh/serializer/issues/1055)

**Merged pull requests:**

- doc update: registerHandler\(\) example [\#1072](https://github.com/schmittjoh/serializer/pull/1072) ([cebe](https://github.com/cebe))
- Updated suggestion for `JsonSerializationVisitor::addData` replacement [\#1066](https://github.com/schmittjoh/serializer/pull/1066) ([theoboldt](https://github.com/theoboldt))
- Add fix to UPGRADING.md [\#1062](https://github.com/schmittjoh/serializer/pull/1062) ([Jean85](https://github.com/Jean85))

## [2.2.0](https://github.com/schmittjoh/serializer/tree/2.2.0) (2019-02-27)

**Implemented enhancements:**

- Add Iterator Handler [\#1034](https://github.com/schmittjoh/serializer/pull/1034) ([scyzoryck](https://github.com/scyzoryck))

**Fixed bugs:**

- xmlRootPrefix missing from unserialized metadata [\#1050](https://github.com/schmittjoh/serializer/issues/1050)
- Non-locale aware encoding of doubles, closes \#1041 [\#1042](https://github.com/schmittjoh/serializer/pull/1042) ([Grundik](https://github.com/Grundik))

**Closed issues:**

- GROUP BY  [\#1051](https://github.com/schmittjoh/serializer/issues/1051)
- Using @Until and @Since on class level [\#1048](https://github.com/schmittjoh/serializer/issues/1048)
- \[Semantical Error\] The annotation \"@generated\" in class JMS\\Serializer\\Type\\InnerParser was never imported [\#1046](https://github.com/schmittjoh/serializer/issues/1046)
- ReflectionException when \(de\)serializing unless fully qualified classname is used [\#1045](https://github.com/schmittjoh/serializer/issues/1045)
- Add use of annotation registry to docs [\#1044](https://github.com/schmittjoh/serializer/issues/1044)
- Values of type "double" should not use locale-specific encoding [\#1041](https://github.com/schmittjoh/serializer/issues/1041)
- SF4: JMS serializer seems to be ignoring global naming strategy [\#1037](https://github.com/schmittjoh/serializer/issues/1037)
- @SerializedName not being ignored since 2.x is bug or feature? [\#1036](https://github.com/schmittjoh/serializer/issues/1036)
- What should I use instead of the dropped GenericDeserializationVisitor class? [\#1035](https://github.com/schmittjoh/serializer/issues/1035)
- DateTime and DateTimeImmutable from PHP 7.1 serialization and deserialization with microseconds [\#1033](https://github.com/schmittjoh/serializer/issues/1033)
- Provide an option to the SerializeBuilder to set AccessType to a specified value globally [\#1025](https://github.com/schmittjoh/serializer/issues/1025)
- Serialize Generator [\#1023](https://github.com/schmittjoh/serializer/issues/1023)

**Merged pull requests:**

- Test on php 7.3 [\#1054](https://github.com/schmittjoh/serializer/pull/1054) ([goetas](https://github.com/goetas))
- xmlRootPrefix missing from unserialized metadata [\#1053](https://github.com/schmittjoh/serializer/pull/1053) ([goetas](https://github.com/goetas))
- Allow @Since and @Until within @VirtualProperty on class level [\#1049](https://github.com/schmittjoh/serializer/pull/1049) ([tjveldhuizen](https://github.com/tjveldhuizen))
- Document use of AnnotationRegistry [\#1047](https://github.com/schmittjoh/serializer/pull/1047) ([andig](https://github.com/andig))
- Fix result of code example [\#1039](https://github.com/schmittjoh/serializer/pull/1039) ([henrikthesing](https://github.com/henrikthesing))

## [2.1.0](https://github.com/schmittjoh/serializer/tree/2.1.0) (2019-01-11)

**Closed issues:**

- Compile error  Declaration of \[...\] must be compatible with \[...\] [\#1024](https://github.com/schmittjoh/serializer/issues/1024)
- Exclude field for depth [\#1022](https://github.com/schmittjoh/serializer/issues/1022)

**Merged pull requests:**

- fixed typo [\#1029](https://github.com/schmittjoh/serializer/pull/1029) ([sasezaki](https://github.com/sasezaki))

## [2.0.2](https://github.com/schmittjoh/serializer/tree/2.0.2) (2018-12-12)

**Fixed bugs:**

- jms serialzier 2.0 Error in debug mode [\#1018](https://github.com/schmittjoh/serializer/issues/1018)
- AbstractDoctrineTypeDriver::normalizeFieldType\(\) must be of the type string, null given [\#1015](https://github.com/schmittjoh/serializer/issues/1015)
- allow empty strings and numbers as metadata type parameters [\#1019](https://github.com/schmittjoh/serializer/pull/1019) ([goetas](https://github.com/goetas))
- internal classes have false in reflection::getFilename\(\) [\#1013](https://github.com/schmittjoh/serializer/pull/1013) ([chregu](https://github.com/chregu))

**Closed issues:**

- DateTime converted to ArrayObject instead of string in custom visitor class [\#1017](https://github.com/schmittjoh/serializer/issues/1017)

**Merged pull requests:**

- Doctrine driver normalizeFieldType method does not handle nulls [\#1020](https://github.com/schmittjoh/serializer/pull/1020) ([goetas](https://github.com/goetas))
- fixed a typo [\#1014](https://github.com/schmittjoh/serializer/pull/1014) ([themasch](https://github.com/themasch))

## [2.0.1](https://github.com/schmittjoh/serializer/tree/2.0.1) (2018-11-29)

## [2.0.0](https://github.com/schmittjoh/serializer/tree/2.0.0) (2018-11-09)

## [2.0.0-RC1](https://github.com/schmittjoh/serializer/tree/2.0.0-RC1) (2018-10-17)

## [2.0.0-beta1](https://github.com/schmittjoh/serializer/tree/2.0.0-beta1) (2018-09-12)

## [1.13.0](https://github.com/schmittjoh/serializer/tree/1.13.0) (2018-07-25)

## [1.12.1](https://github.com/schmittjoh/serializer/tree/1.12.1) (2018-06-01)

## [1.12.0](https://github.com/schmittjoh/serializer/tree/1.12.0) (2018-05-25)

## [1.11.0](https://github.com/schmittjoh/serializer/tree/1.11.0) (2018-02-04)

## [1.10.0](https://github.com/schmittjoh/serializer/tree/1.10.0) (2017-11-30)

## [1.9.2](https://github.com/schmittjoh/serializer/tree/1.9.2) (2017-11-22)

## [1.9.1](https://github.com/schmittjoh/serializer/tree/1.9.1) (2017-10-27)

## [1.9.0](https://github.com/schmittjoh/serializer/tree/1.9.0) (2017-09-28)

## [1.8.1](https://github.com/schmittjoh/serializer/tree/1.8.1) (2017-07-13)

## [1.8.0](https://github.com/schmittjoh/serializer/tree/1.8.0) (2017-07-12)

## [1.7.1](https://github.com/schmittjoh/serializer/tree/1.7.1) (2017-05-15)

## [1.7.0](https://github.com/schmittjoh/serializer/tree/1.7.0) (2017-05-10)

## [1.7.0-RC2](https://github.com/schmittjoh/serializer/tree/1.7.0-RC2) (2017-05-05)

## [1.7.0-RC1](https://github.com/schmittjoh/serializer/tree/1.7.0-RC1) (2017-04-25)

## [1.6.2](https://github.com/schmittjoh/serializer/tree/1.6.2) (2017-04-17)

## [1.6.1](https://github.com/schmittjoh/serializer/tree/1.6.1) (2017-04-12)

## [1.6.0](https://github.com/schmittjoh/serializer/tree/1.6.0) (2017-03-24)

## [1.6.0-RC1](https://github.com/schmittjoh/serializer/tree/1.6.0-RC1) (2017-03-14)

## [1.5.0](https://github.com/schmittjoh/serializer/tree/1.5.0) (2017-02-14)

## [1.5.0-RC1](https://github.com/schmittjoh/serializer/tree/1.5.0-RC1) (2017-01-19)

## [1.4.2](https://github.com/schmittjoh/serializer/tree/1.4.2) (2016-11-13)

## [1.4.1](https://github.com/schmittjoh/serializer/tree/1.4.1) (2016-11-02)

## [1.4.0](https://github.com/schmittjoh/serializer/tree/1.4.0) (2016-10-31)

## [1.3.1](https://github.com/schmittjoh/serializer/tree/1.3.1) (2016-08-23)

## [1.3.0](https://github.com/schmittjoh/serializer/tree/1.3.0) (2016-08-17)

## [1.2.0](https://github.com/schmittjoh/serializer/tree/1.2.0) (2016-08-03)

## [1.1.0](https://github.com/schmittjoh/serializer/tree/1.1.0) (2015-10-27)

## [1.0.0](https://github.com/schmittjoh/serializer/tree/1.0.0) (2015-06-16)

## [0.16.0](https://github.com/schmittjoh/serializer/tree/0.16.0) (2014-03-18)

## [0.15.0](https://github.com/schmittjoh/serializer/tree/0.15.0) (2014-02-10)

## [0.14.0](https://github.com/schmittjoh/serializer/tree/0.14.0) (2013-12-04)

## [0.13.0](https://github.com/schmittjoh/serializer/tree/0.13.0) (2013-07-29)

## [0.12.0](https://github.com/schmittjoh/serializer/tree/0.12.0) (2013-03-28)

## [0.11.0](https://github.com/schmittjoh/serializer/tree/0.11.0) (2013-01-29)



\* *This Changelog was automatically generated by [github_changelog_generator](https://github.com/github-changelog-generator/github-changelog-generator)*
