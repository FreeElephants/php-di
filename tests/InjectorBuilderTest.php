<?php

namespace FreeElephants\DI;

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

        $injector = $builder->buildFromArray([
            'instances' => [
                Bar::class => new Bar(),
            ],
            'register' => [
                Foo::class
            ]
        ]);

        $this->assertInstanceOf(Foo::class, $injector->createInstance(Foo::class));
    }
}