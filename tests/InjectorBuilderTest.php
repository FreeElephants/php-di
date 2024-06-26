<?php

namespace FreeElephants\DI;

use Fixture\AnotherLoggerAwareClass;
use Fixture\AnotherService;
use Fixture\Bar;
use Fixture\ClassWithDefaultConstructorArgValue;
use Fixture\Foo;
use Fixture\LoggerAwareClass;
use Psr\Container\ContainerInterface;
use Psr\Log\NullLogger;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class InjectorBuilderTest extends AbstractTestCase
{

    public function testBuildFromArray()
    {
        $builder = new InjectorBuilder();

        $anotherServiceInstance = new AnotherService();
        $bar1 = new Bar();
        $bar2 = new Bar();
        $injector = $builder->buildFromArray([
            'instances' => [
                Bar::class => $bar1,
                $anotherServiceInstance,
            ],
            'register'  => [
                Foo::class
            ],
            'callable'  => [
                ClassWithDefaultConstructorArgValue::class => [
                    function (ContainerInterface $container, int $value): ClassWithDefaultConstructorArgValue {
                        return new ClassWithDefaultConstructorArgValue($value);
                    },
                    9000,
                ],
                Bar::class                                 => function () use ($bar2) {
                    return $bar2;
                },
            ],
            'loggers'   => [
                LoggerAwareClass::class        => new NullLogger(),
                AnotherLoggerAwareClass::class => new NullLogger(),
            ],
        ]);

        $this->assertSame($bar2, $injector->getService(Bar::class));
        $this->assertInstanceOf(Foo::class, $injector->createInstance(Foo::class));
        $this->assertSame($anotherServiceInstance, $injector->getService(AnotherService::class));
        $this->assertSame(9000, $injector->get(ClassWithDefaultConstructorArgValue::class)->getValue());

        $this->assertCount(2, $injector->getLoggersMap());
    }
}
