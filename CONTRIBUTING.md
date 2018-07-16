# Contribute

Thank you for contributing!

Before we can merge your Pull-Request here are some guidelines that you need to follow. 
These guidelines exist not to annoy you, but to keep the code base clean, unified and future proof.

## Coding Standard

This project uses [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) to enforce coding standards.
The coding standard rules are defined in the **phpcs.xml.dist** file (part of this repository).
The project follows a relaxed version of the Doctrine Coding standards v4.

Your Pull-Request must be compliant with the said standard.
To check your code you can run `vendor/bin/phpcs`. This command will give you a list of violations in your code (if any).
The most common errors can be automatically fixed just by running `vendor/bin/phpcbf`.

## Unit-Tests

Please try to add a test for your pull-request. This project uses [PHPUnit](https://phpunit.de/) as testing framework.

You can run the unit-tests by calling `vendor/bin/phpunit`.

New features without tests can't be merged.

## CI

We automatically run your pull request through [Travis CI](https://www.travis-ci.org) 
and [Scrutinizer CI](https://scrutinizer-ci.com/).
If you break the tests, we cannot merge your code,
so please make sure that your code is working before opening up a Pull-Request.

## Issues and Bugs

To create a new issue, you can use the GitHub issue tracking system.
Please try to avoid opening support-related tickets. For support related questions please use more appropriate
channels as Q&A platforms (such as Stackoverflow), Forums, Local PHP user groups.

If you are a Symfony user, please try to distinguish between issues related to the Bundle and issues related to this 
library.  

## Getting merged

Please allow us time to review your pull requests.
We will give our best to review everything as fast as possible, but cannot always live up to our own expectations.

Thank you very much again for your contribution!
