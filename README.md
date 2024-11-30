# Simple PHP Constructor-based Dependency Injection

[![Build Status](https://github.com/FreeElephants/php-di/workflows/CI/badge.svg)](https://github.com/FreeElephants/php-di/actions)
[![codecov](https://codecov.io/gh/FreeElephants/php-di/branch/master/graph/badge.svg)](https://codecov.io/gh/FreeElephants/php-di)
[![Installs](https://img.shields.io/packagist/dt/free-elephants/di.svg)](https://packagist.org/packages/free-elephants/di)
[![Releases](https://img.shields.io/packagist/v/free-elephants/di.svg)](https://github.com/FreeElephants/php-di/releases)

_Configure less. Do more._  

Framework-agnostic Dependency Injection tool and PSR-11 implementation provider. 

## Requirements

PHP 7.4|8+

## Installation

```bash
composer require free-elephants/di
```

## Usage

Your entry php script (index.php or some background-job runner)
```php
$components = require 'components.php';
$di = (new \FreeElephants\DI\InjectorBuilder)->buildFromArray($components);
$app = $di->createInstance(\YourApplication::class);
$app->run();
```

Your `components.php` file with dependencies description should look like this:

```php
<?php

return [
    'instances' => [
        \PDO::class => new \PDO(getenv('DB_DNS'), getenv('DB_USER'), getenv('DB_PASS')),
    ],
    'register' => [
        \YourApplication::class,
        \ControllerFactory::class,
        \SomeService::class,
        \AnotherService::class,
        \Psr\Log\LoggerInterface::class => \Symfony\Component\Console\Logger\ConsoleLogger::class,
    ],
    'callable' => [ 
        // if function provided as key value
        // first argument passed to callable is psr container
        // second is key
        Foo::class => function(\Psr\Container\ContainerInterface $container, string $key) {
           return (new Foo())->setSomething($container->get('something'));
        },
        // if array provided as key value
        // first argument passed to callable is psr container
        // remaining element as ...args tail
        Bar::class => [ // array where first element is callable, other is values for last arguments
            function(\Psr\Container\ContainerInterface $container, $firstArg, string $secondArg) {
                return new Bar($firstArg, $secondArg);
            },
            100,
            500,
        ],       
    ],
    'loggers' => [
       // For suitable logger injections use map, where keys are your services, that implement LoggerAwareInterface
       // and value is logger instances or callable with logic for instantiate it. 
       LoggerAwareClass::class        => $logger,
       AnotherLoggerAwareClass::class => fn(\Psr\Container\ContainerInterface $container) => new \Psr\Log\NullLogger(),
    ],   
];
```

The main idea: all your components should expect all dependencies as constructor arguments.  All other work entrust to Injector.
You do not have to want to instantiate any classes directly in your code. Your must inject some factories instead.   

### Override Components by Environments

```php
<?php
// getenv('ENV') -> 'test'
$components = (new \FreeElephants\DI\EnvAwareConfigLoader(__DIR__ . '/config', 'ENV'))->readConfig('components');
$di = (new \FreeElephants\DI\InjectorBuilder)->buildFromArray($components);
```

`EnvAwareConfigLoader` load `config/components.php` and merge it with `config/components.test.php` if it exists.

## Options:

### `allowNullableConstructorArgs`

Default value is `false`.  

### `allowInstantiateNotRegisteredTypes` 

Default value is `false`. When you set it `true`, you can register only specific interfaces instances. 
All final typed dependency will be lazy-instantiated by chain!  

### `useIdAsTypeName`

Default value is `true`. 

### `enableLoggerAwareInjection`

Default value is `false`.

Allow to set LoggerInterface into LoggerAwareInterface instances after constructing, or use loggers map if type present.   

## Conception

## [In Russian] Простейшее внедрение зависимостей через конструктор для PHP 

В ООП можно выделить две большие группы классов по их ответственности: сущности и сервисы. 

**Сущности** содержат данные и  методы для работы с ними. 
Они чаще всего обладают состоянием и требуются во множестве экзепляров, создаваемых во время исполнения программы. 
Например:
- доменные объекты (например Entities в контексте Doctrine, Models у Propel)
- Value Objects
- объекты для передачи данных (Data Transfer Objects)
- Request / Response из PSR 7

**Сервисы** отвечают за всё остальное: 
- обрабатывают запрос пользователя (контроллеры, команды)
- оперируют сущностями
- обеспечивают коммуникацию между компонентами системы
- инстанцируют другие сущности и сервисы (фабрики, локаторы)
- предоставляют прикладную функциональность (протоколы, хранилища, роутинг) 

Сервис часто требуются в единственном экземпляре, и, редко меняет собственное состояние во время исполнения. 
Сервисы могут быть описаны до этапа исполнения, например в статическом файле, и быть получены в коде по требованию, или созданы единожды при запуске приложения. 

Сущности не должны зависеть от сервисов. В то время, как сервисы наоборот — часто оперируют экземплярами сущностей. 
При этом сервисы могут использовать другие сервисы. 

Наиболее явный и надёжный способ внедрения зависимостей, это инъекция в конструктор:
- нельзя создать экземпляр неготовый к использованию
- зависимости класса обозначены контрактом в одном месте

Type Hinting и рефлексия в php позволяют собрать готовый к использованию сервис на основе сигнатуры его конструктора, без лишних конфигурационных файлов и магии. 
Такой подход использован в free-elephants/di. 
Этот способ хорошо поддерживает рефакторинг, т.к. используется только нативный php-код, не требует статического описания зависимостей в yml, xml или аннотациях. 
