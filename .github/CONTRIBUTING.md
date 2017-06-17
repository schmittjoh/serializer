# CONTRIBUTING

First of all, **thank you** for contributing, **you are awesome**!

Before we can merge your Pull-Request here are some guidelines that you need to follow. 
These guidelines exist not to annoy you, but to keep the code base clean, unified and future proof.

Thank you for contributing!

## Pull Request Description 

While creating your Pull Request on GitHub, you must write a description
which gives the context and/or explains why you are creating it.

## Dependencies

We're using [`composer/composer`](https://github.com/composer/composer) to manage dependencies

## Coding Standard

We are using the latest [PSR](http://www.php-fig.org/psr/) recommendations.

## Unit-Tests

We're using [`phpunit/phpunit`](https://github.com/sebastianbergmann/phpunit) to run tests.

You can run the unit-tests by calling `vendor/bin/phpunit` from the root of the project.

If you are changing code, you must update or add tests for your changes.


## Travis

We automatically run your pull request through [Travis CI](http://www.travis-ci.org).
If you break the tests, we cannot merge your code,
so please make sure that your code is working before opening up a Pull-Request.

## Documentation

If you are adding a new feature, you must update the documentation.

## Issues and Bugs

To create a new issue, you can use the GitHub issue tracking system.

Please avoid creating issues for support requests,
please read carefully the project documentation, 
try more appropriate channels as forums, questions and answers platforms...

Issues considered as "support request" will be closed, 
the discussion can continue on a closed issue, maintainers will do their best to help.

## Getting merged

Please allow us time to review your pull requests.
We will give our best to review everything as fast as possible, 
but cannot always live up to our own expectations.

Please, write [commit messages that make
sense](http://tbaggery.com/2008/04/19/a-note-about-git-commit-messages.html),
and [rebase your branch](http://git-scm.com/book/en/Git-Branching-Rebasing)
before submitting your Pull Request.

One may ask you to [squash your
commits](http://gitready.com/advanced/2009/02/10/squashing-commits-with-rebase.html)
too. This is used to "clean" your Pull Request before merging it (we don't want
commits such as "fix tests", "fix 2", "fix 3", etc.).

Pull requests without tests most probably will not be merged.
Documentation PRs obviously do not require tests.

