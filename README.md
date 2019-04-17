# jms/serializer 


[![Build status][Master image]][Master] 
[![Coverage Status][Master coverage image]][Master coverage] 

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

You are browsing the code for the 3.x version, if you are interested in the 1.x or 2.x version, 
check the [1.x][1.x] and [2.x][2.x] branches.

Instructions on how to upgrade available in the [UPGRADING][UPGRADING] document. 

- `3.x` is the active and supported version (`master` branch).
- `2.x` is not supported anymore (`2.x` branch).
- `1.x` is in maintenance mode, there will be no active development but PRs are accepted (`1.x` branch). 
 
Bug fixes sent on the 1.x branch, will be ported to the master branch when possible.

  [CHANGELOG]: https://github.com/schmittjoh/serializer/blob/master/CHANGELOG.md
  [UPGRADING]: https://github.com/schmittjoh/serializer/blob/master/UPGRADING.md
  
  [Master image]: https://img.shields.io/travis/schmittjoh/serializer/master.svg?style=flat-square
  [Master]: https://travis-ci.org/schmittjoh/serializer
  [Master coverage image]: https://img.shields.io/scrutinizer/coverage/g/schmittjoh/serializer/master.svg?style=flat-square
  [Master coverage]: https://scrutinizer-ci.com/g/schmittjoh/serializer/?branch=master
  
  [1.x]: https://github.com/schmittjoh/serializer/tree/1.x
  [2.x]: https://github.com/schmittjoh/serializer/tree/2.x
