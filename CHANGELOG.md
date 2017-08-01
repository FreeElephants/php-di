# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

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