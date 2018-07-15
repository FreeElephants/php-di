<?php

namespace FreeElephants\DI;

use Fixture\AnotherService;
use Fixture\AnotherServiceInterface;
use Fixture\Bar;
use Fixture\BarChild;
use Fixture\ClassWithDefaultConstructorArgValue;
use Fixture\ClassWithNullableConstructorArgs;
use Fixture\ClassWithTypedScalarConstructorArgDefaultValue;
use Fixture\DefaultAnotherServiceImpl;
use Fixture\Foo;
use Fixture\LoggerAwareClass;
use Fixture\SomeService;
use Fixture\SomeServiceInterface;
use FreeElephants\DI\Exception\InvalidArgumentException;
use FreeElephants\DI\Exception\OutOfBoundsException;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

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
        $this->assertSame($bar, $foo->getBar());
    }

    public function testHasImplementation()
    {
        $injector = new Injector();
        $this->assertFalse($injector->hasImplementation(Bar::class));
        $injector->registerService(Bar::class);
        $injector->hasImplementation(Bar::class);
    }

    public function testGetLoggerHelper()
    {
        $injector = new Injector();
        $this->assertInstanceOf(LoggerHelper::class, $injector->getLoggerHelper());
    }

    public function testCreateInstanceWithoutConstructor()
    {
        $injector = new Injector();

        $bar = $injector->createInstance(Bar::class);

        $this->assertInstanceOf(Bar::class, $bar);
    }

    public function testRegisterService()
    {
        $injector = new Injector();

        $injector->registerService(SomeService::class, SomeServiceInterface::class);
        $injector->registerService(AnotherService::class, AnotherServiceInterface::class);
        /**@var $someService SomeServiceInterface */
        $someService = $injector->getService(SomeServiceInterface::class);
        $anotherService = $injector->getService(AnotherServiceInterface::class);

        $this->assertSame($anotherService, $someService->getAnotherService());
    }

    public function testGetNotRegistredService()
    {
        $injector = new Injector();

        $this->expectException(OutOfBoundsException::class);

        $injector->getService(Foo::class);
    }

    public function testGetNotRegistredServiceWithAllowedNullableConstructorArgs()
    {
        $injector = new Injector();
        $injector->allowNullableConstructorArgs(true);
        /**@var $classWithNullableConstructorArgsInstance  ClassWithNullableConstructorArgs */
        $classWithNullableConstructorArgsInstance = $injector->createInstance(ClassWithNullableConstructorArgs::class);
        $this->assertInstanceOf(DefaultAnotherServiceImpl::class,
            $classWithNullableConstructorArgsInstance->getAnotherService());
    }

    public function testGetNotRegistredServiceWithNotAllowedNullableConstructorArgs()
    {
        $injector = new Injector();

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Requested service with type Fixture\AnotherServiceInterface is not set. [Required in Fixture\ClassWithNullableConstructorArgs constructor]');
        $injector->createInstance(ClassWithNullableConstructorArgs::class);
    }

    public function testDefaultConstructorArgsValueWithoutType()
    {
        $injector = new Injector();
        /**@var $instance ClassWithDefaultConstructorArgValue */
        $instance = $injector->createInstance(ClassWithDefaultConstructorArgValue::class);
        $this->assertSame(100500, $instance->getValue());
    }

    public function testRegisterServiceReplacement()
    {
        $injector = new Injector();
        $injector->registerService(Bar::class);
        $injector->registerService(BarChild::class, Bar::class);
        $this->assertInstanceOf(BarChild::class, $injector->getService(Bar::class));
    }

    public function testSetNotMatchedTypeServiceInstance()
    {
        $injector = new Injector();

        $this->expectException(InvalidArgumentException::class);

        $injector->setService(Foo::class, new Bar());
    }

    public function testMerge()
    {
        $injector = new Injector();
        $bar1 = new Bar();
        $bar2 = new Bar();
        $injector->setService(Bar::class, $bar1);

        $injector->merge([
            'instances' => [
                Bar::class => $bar2,
            ]
        ]);
        $this->assertSame($bar2, $injector->getService(Bar::class));
    }

    public function testCreateNotRegistredTypeInstance()
    {
        $injector = new Injector();
        $injector->allowInstantiateNotRegisteredTypes(true);
        $this->assertInstanceOf(Foo::class, $injector->getService(Foo::class));
    }

    public function testRegisterItSelf()
    {
        $injector = new Injector();
        $this->assertFalse($injector->hasImplementation(Injector::class));
        $injector->registerItSelf();
        $this->assertTrue($injector->hasImplementation(Injector::class));
    }

    public function testDefaultScalarValue_with_AllowedNotRegisteredTypesInstantiation()
    {
        $injector = new Injector();
        $injector->allowInstantiateNotRegisteredTypes(true);
        $injector->allowNullableConstructorArgs(true);
        $injector->createInstance(ClassWithTypedScalarConstructorArgDefaultValue::class);
    }

    public function testGet()
    {
        $injector = new Injector();
        $injector->useIdAsTypeName(false);
        $injector->registerService(Bar::class, 'bar');
        $this->assertInstanceOf(Bar::class, $injector->get('bar'));
    }

    public function testGetNotFoundException()
    {
        $injector = new Injector();
        $this->expectException(NotFoundExceptionInterface::class);
        $injector->get('bar');
    }

    public function testHas()
    {
        $injector = new Injector();
        $this->assertFalse($injector->has('bar'));
        $injector->useIdAsTypeName(false);
        $injector->registerService(Bar::class, 'bar');
        $this->assertTrue($injector->has('bar'));
    }

    public function testLoggerInjection()
    {
        $logger = new NullLogger();

        $injector = new Injector();
        $injector->setService(LoggerInterface::class, $logger);
        $injector->enableLoggerAwareInjection(true);

        /**@var LoggerAwareClass $loggerAware */
        $loggerAware = $injector->createInstance(LoggerAwareClass::class);
        $this->assertSame($logger, $loggerAware->getLogger());

    }

}

