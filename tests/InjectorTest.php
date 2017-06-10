<?php

namespace FreeElephants\DI;

use Fixture\AnotherService;
use Fixture\AnotherServiceInterface;
use Fixture\SomeService;
use Fixture\SomeServiceInterface;
use Fixture\Bar;
use Fixture\Foo;
use FreeElephants\DI\Exception\InvalidArgumentException;
use FreeElephants\DI\Exception\OutOfBoundsException;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class InjectorTest extends \PHPUnit_Framework_TestCase
{

    public function testInjectionToConstructor()
    {
        $injector = new Injector();
        $bar = new Bar();
        $injector->setService(Bar::class, $bar);
        $foo = $injector->createInstance(Foo::class);
        /**@var $foo Foo */
        self::assertSame($bar, $foo->getBar());
    }

    public function testCreateInstanceWithoutConstructor()
    {
        $injector = new Injector();
        $bar = $injector->createInstance(Bar::class);
        self::assertInstanceOf(Bar::class, $bar);
    }

    public function testRegisterService()
    {
        $injector = new Injector();
        $injector->registerService(SomeService::class, SomeServiceInterface::class);
        $injector->registerService(AnotherService::class, AnotherServiceInterface::class);
        /**@var $someService SomeServiceInterface*/
        $someService = $injector->getService(SomeServiceInterface::class);
        $anotherService = $injector->getService(AnotherServiceInterface::class);
        self::assertSame($anotherService, $someService->getAnotherService());
    }

    public function testGetNotRegistredService()
    {
        $injector = new Injector();
        $this->expectException(OutOfBoundsException::class);
        $injector->getService(Foo::class);
    }

    public function testSetNotMatchedTypeServiceInstance()
    {
        $injector = new Injector();
        $this->expectException(InvalidArgumentException::class);
        $injector->setService(Foo::class, new Bar());
    }

}