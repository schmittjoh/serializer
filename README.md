# jms/serialzer 

| [Master][Master] | [1.x][1.x] |
|:----------------:|:----------:|
| [![Build status][Master image]][Master] | [![Build status][1.x image]][1.x] |
| [![Coverage Status][Master coverage image]][Master coverage] | [![Coverage Status][1.x coverage image]][1.x coverage] | 

![alt text](doc/logo-small.png)

## Introduction

This library allows you to (de-)serialize data of any complexity. Currently, it supports XML and JSON.

It also provides you with a rich tool-set to adapt the output to your specific needs.

Built-in features include:

- (De-)serialize data of any complexity; circular references and complex exclusion strategies are handled gracefully.
- Supports many built-in PHP types (such as dates, intervals)
- Integrates with Doctrine ORM, et. al.
- Supports versioning, e.g. for APIs
- Configurable via XML, YAML, or Annotations

   
## Documentation

Learn more about the serializer in its [documentation](http://jmsyst.com/libs/serializer).

## Notes

You are browsing the code for the 2.x version, if you are interested in the 1.x version, 
check the [1.x][1.x] branch.

Differences between the 1.x and 2.x can be found in the [CHANGELOG][CHANGELOG]. 
Upgrading from 1.x to 2.x should be almost transparent for most of the userland code, in the  
case you have heavily used internal-api or you are relaying on some of the removed features,
the [UPGRADING][UPGRADING] document is a short guide on how to upgrade. 

Pull requests for new features are accepted only on the master branch. 
Bug fixes are accepted for both master and 1.x branches. 
Bug fixes sent on the 1.x branch, will be ported to the master branch when possible. 

  [CHANGELOG]: https://github.com/schmittjoh/serializer/blob/master/CHANGELOG.md
  [UPGRADING]: https://github.com/schmittjoh/serializer/blob/master/UPGRADING.md
  
  [Master image]: https://img.shields.io/travis/schmittjoh/serializer/master.svg?style=flat-square
  [Master]: https://travis-ci.org/schmittjoh/serializer
  [Master coverage image]: https://img.shields.io/scrutinizer/coverage/g/schmittjoh/serializer/master.svg?style=flat-square
  [Master coverage]: https://scrutinizer-ci.com/g/schmittjoh/serializer/?branch=master
  
  [1.x image]: https://img.shields.io/travis/schmittjoh/serializer/1.x.svg?style=flat-square
  [1.x]: https://github.com/schmittjoh/serializer/tree/1.x
  [1.x coverage image]: https://img.shields.io/scrutinizer/coverage/g/schmittjoh/serializer/1.x.svg?style=flat-square
  [1.x coverage]: https://scrutinizer-ci.com/g/schmittjoh/serializer/?branch=1.x
  
