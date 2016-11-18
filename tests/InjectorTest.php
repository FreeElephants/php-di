<?php

namespace FreeElephants\DI;

use FreeElephants\DI\Fixture\Bar;
use FreeElephants\DI\Fixture\Foo;

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
}