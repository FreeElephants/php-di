# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## [3.1.1] - 2021-09-02
### Changed
- Support all versions of psr/log and psr/container 

## [3.1.0] - 2021-05-17
### Added:
- 'callable' di configuration entries support

### Changed
- Register itself as PSR Container 

## [3.0.0] - 2020-12-19
### Added
- PHP 8 support

### Changed
- Check that argument type is not builtin, instead deprecated `ReflectionType::getClass` usage

### Removed
- PHP 7.1 and 7.2 support

## [2.1.0]
### Added
- ConfigLoaderInterface and EnvAwareConfigLoader.
- MissingDependencyException (implements PSR interface), generally uses resolving errors. 

### Fixed
- [#10]: Throw MissingDependencyException instead Uncaught Error on missing abstraction.

### Changed
- OutOfBoundsException mark internal, handle MissingDependencyException instead.

## [2.0.2] - 2020-01-20
### Fixed
- Use ReflectionNamedType::getName() instead deprecated ReflectionType::__toString(). 

## [2.0.1] - 2019-05-26
### Changed
- Up minimal stability. 

## [2.0.0] - 2019-05-26
### Added 
- PHP 7.3-7.4 support. 

### Removed
- PHP 7 support (end of life). 

## [1.7.0] - 2018-07-22
### Added
- PSR-3 optional support: enable with `Injector::enableLoggerAwareInjection()`. Disabled by default. 

### Fixed
- License name according SPDX.

## [1.6.0] - 2017-10-29
### Added
- Psr-11 ContainerInterface implementation. 
- Option `useIdAsTypeName` (default `true`) for control instance of checking for registered classes: disable with `Injector::useIdAsTypeName(false)`.  

## [1.5.1] - 2017-10-18
### Fixed 
- Check that argument has class (e.g. not a scalar) before try instantiate it. 

## [1.5.0] - 2017-10-18
### Added
- Allow creating instances of not registered types: enable with `Injector::allowInstantiateNotRegisteredTypes(true)`.
- Register Injector itself (useful for injection to factories): use `Injector::registerItself()`. 

## [1.4.0] - 2017-08-17
### Added
- Allow using default value if required type not set in container: enable with `Injector::allowNullableConstructorArgs(true)`.

## [1.3.0] - 2017-07-31
### Added
- Method Injector::merge()
- Method Injector::getLoggerHelper()

### Changed
- LoggerHelper implement PSR LoggerAwareInterface

## [1.2.0] - 2017-06-11 
### Changed
- Allow use value without key with interface name: for instances will be used class, for registered types -- self class name.  

## [1.1.0] - 2017-06-10 
### Added
- InjectorBuilder class. 

### Internal 
- Travis-CI integration. 

## [1.0.0] - 2016-12-01
### Added
- All classes. 

[Unreleased]: https://github.com/FreeElephants/php-di/compare/3.1.1...HEAD
[3.1.1]: https://github.com/FreeElephants/php-di/compare/3.1.0...3.1.1
[3.1.0]: https://github.com/FreeElephants/php-di/compare/3.0.0...3.1.0
[3.0.0]: https://github.com/FreeElephants/php-di/compare/2.1.0...3.0.0
[2.1.0]: https://github.com/FreeElephants/php-di/compare/2.0.2...2.1.0
[2.0.2]: https://github.com/FreeElephants/php-di/compare/2.0.1...2.0.2
[2.0.1]: https://github.com/FreeElephants/php-di/compare/2.0.0...2.0.1
[2.0.0]: https://github.com/FreeElephants/php-di/compare/1.7.0...2.0.0
[1.7.0]: https://github.com/FreeElephants/php-di/compare/1.6.0...1.7.0
[1.6.0]: https://github.com/FreeElephants/php-di/compare/1.5.1...1.6.0
[1.5.1]: https://github.com/FreeElephants/php-di/compare/1.5.0...1.5.1
[1.5.0]: https://github.com/FreeElephants/php-di/compare/1.4.0...1.5.0
[1.4.0]: https://github.com/FreeElephants/php-di/compare/1.3.0...1.4.0
[1.3.0]: https://github.com/FreeElephants/php-di/compare/1.2.0...1.3.0
[1.2.0]: https://github.com/FreeElephants/php-di/compare/1.1.0...1.2.0
[1.1.0]: https://github.com/FreeElephants/php-di/compare/1.0.0...1.1.0
