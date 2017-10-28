# Simple PHP Constructor-based Dependency Injection

[![Build Status](https://travis-ci.org/FreeElephants/php-di.svg?branch=master)](https://travis-ci.org/FreeElephants/php-di)

[![codecov](https://codecov.io/gh/FreeElephants/php-di/branch/master/graph/badge.svg)](https://codecov.io/gh/FreeElephants/php-di)

[![Installs](https://img.shields.io/packagist/dt/free-elephants/di.svg)](https://packagist.org/packages/free-elephants/di)

[![Releases](https://img.shields.io/packagist/v/free-elephants/di.svg)](https://github.com/FreeElephants/php-di/releases)

_Configure less. Do more._  

Framework-agnostic Dependency Injection tool and PSR-11 implementation provider. 

## Requirements

PHP >=7.0

## Installation

```bash
composer install free-elephants/di
```

## Usage

Your entry php script (index.php or some background-job runner)
```php
$components = require 'components.php';
$di = (new \FreeElephants\DI\InjectorBuilder)->buildFromArray($components);
$app = $di->createInstance(YourApplication::class);
$app->run();
```

Your `components.php` file with dependencies description shoud look like this:
```php
<?php

return [
    'instanses' => [
        PDO::class => new PDO(getenv('DB_DNS'), getenv('DB_USER'), getenv('DB_PASS')),
    ],
    'register' => [
        YourApplication::class,
        ControllerFactory::class,
        SomeService::class,
        AnotherService::class,
        \Psr\Log\LoggerInterface::class => Symfony\Component\Console\Logger\ConsoleLogger::class
        // etc
    ],
];
```

The main idea: all your components should accept all dependencies as constuctor arguments.  All other work entrust to Injector.
You do not have to want instantiate any classes directly in your code. Your must inject some factories enstead.   

## Options:

### `allowNullableConstructorArgs`
Default value is `false`.  

### `allowInstantiateNotRegisteredTypes` 
Default value is `false`. When you set it `true`, you can register only specific interfaces instances. All final typed dependency will be lazy-instantiated by chain!  

### `useIdAsTypeName`
Default value is `true`. 

## Conception

## [In Russian] Простейшее внедрение зависимостей через конструктор для PHP 

В объектно-ориентированном приложении в общем случае все классы можно разделить на две категории: сущности и сервисы

Сущности в себе хранят данные и простые методы для манипуляции с ними. Они обладают состоянием и в большинстве случаев, требуются во множестве экзепляров, создаваемых во время испольнения программы. 
Под сущностями мы подразумеваем:
- доменные объекты (например Entities в контексте Doctrine, Models у Propel)
- объекты для передаци данных (Data Transfer Objects)
- Request / Response из PSR 7. 

Сервисы отвечают за всё остальное: 
- обеспечивают коммуникацию между компонентами системы, 
- содержат бизнес-логику,
- оперируют сущностями
- инстанцируют другие сущности и сервисы (фабрики, локаторы)
- предоставляют прикладную функциональность (протоколы, хранилища, роутинг) 

Сервис, как правило, требуется в единственном экземпляре, и, в идеальном случае не обладает меняющимся во время испольнения состоянием. В принципе сервисы могут быть описаны до этапа исполнения, например в статическом файле, и быть получены в коде по требованию, или созданы единожды при запуске приложения.   
 
Сущности не должны зависить от сервисов. В то время, как сервисы наоборот — оперируют в большинстве случаев экземплярами сущностей. При этом сервисам свойственно использовать другие сервисы. 

Наиболее явный и надёжный способ для внедрения зависимостей, это инъекция в конструктор.   
