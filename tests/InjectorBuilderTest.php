<?php

namespace FreeElephants\DI;

use Fixture\AnotherService;
use Fixture\Bar;
use Fixture\Foo;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class InjectorBuilderTest extends \PHPUnit_Framework_TestCase
{

    public function testBuildFromArray()
    {
        $builder = new InjectorBuilder();

        $anotherServiceInstance = new AnotherService();
        $injector = $builder->buildFromArray([
            'instances' => [
                Bar::class => new Bar(),
                $anotherServiceInstance,
            ],
            'register' => [
                Foo::class
            ]
        ]);

        $this->assertInstanceOf(Foo::class, $injector->createInstance(Foo::class));
        $this->assertSame($anotherServiceInstance, $injector->getService(AnotherService::class));
    }
}