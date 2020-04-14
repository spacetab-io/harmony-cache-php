# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2020-04-14

### Added

*None*

### Changed

* `HarmonyIO\Cache\Provider\Redis` constructor takes parameter with a `Amp\Redis\Redis` object instead of old `Amp\Redis\Client`.
* Rewritten tests to use official `amphp/phpunit-util` package instead of `harmonyio/phpunit-extensions`.

### Deprecated

*None*

### Removed

* `harmonyio/phpunit-extensions` composer package.

### Fixed

* PHP 7.4 compatibility (in tests).

### Security

*None*
