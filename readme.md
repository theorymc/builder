# Theory Builder

[![Build Status](http://img.shields.io/travis/theorymc/builder.svg?style=flat-square)](https://travis-ci.org/theorymc/builder)
[![Version](http://img.shields.io/packagist/v/theory/builder.svg?style=flat-square)](https://packagist.org/packages/theory/theory-builder)
[![License](http://img.shields.io/packagist/l/theory/builder.svg?style=flat-square)](license.md)

An object-oriented approach to server modification.

## Installation

```sh
$ composer require theory/builder
```

## Examples

```php
$builder = new Client("127.0.0.1", 25575, $password = "hello", $timeout = 3);
$builder->exec("/say hello world");
```

## Versioning

This library follows [Semver](http://semver.org). According to Semver, you will be able to upgrade to any minor or patch version of this library without any breaking changes to the public API. Semver also requires that we clearly define the public API for this library.

All methods, with `public` visibility, are part of the public API. All other methods are not part of the public API. Where possible, we'll try to keep `protected` methods backwards-compatible in minor/patch versions, but if you're overriding methods then please test your work before upgrading.
