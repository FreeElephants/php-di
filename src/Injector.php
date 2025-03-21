<?php

namespace FreeElephants\DI;

use FreeElephants\DI\Exception\InvalidArgumentException;
use FreeElephants\DI\Exception\MissingDependencyException;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class Injector implements ContainerInterface
{

    private array $serviceMap = [];

    private LoggerHelper $loggerHelper;

    private bool $allowNullableConstructorArgs = false;

    private bool $allowInstantiateNotRegisteredTypes = false;

    private bool $useIdAsTypeName = true;

    private bool $enableLoggerAwareInjection = false;

    private array $loggersMap = [];

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->loggerHelper = new LoggerHelper($logger ?: new NullLogger());
    }

    public function getLoggerHelper(): LoggerHelper
    {
        return $this->loggerHelper;
    }

    public function setService(string $typeName, $service): void
    {
        if ($this->useIdAsTypeName && false === $service instanceof $typeName) {
            $this->loggerHelper->logNotMatchedTypeInstance($typeName, $service);
            throw new InvalidArgumentException('Given instance not belong to this type. ');
        } else {
            if (isset($this->serviceMap[$typeName]) && is_object($this->serviceMap[$typeName])) {
                $this->loggerHelper->logServiceInstanceReplacing($typeName, $service, $this->serviceMap[$typeName]);
            }
            $this->serviceMap[$typeName] = $service;
        }
        $this->loggerHelper->logServiceSetting($typeName, $service);
    }

    public function createInstance($class): object
    {
        $reflectedClass = new \ReflectionClass($class);
        if ($reflectedClass->isAbstract() || $reflectedClass->isInterface()) {
            $message = sprintf('%s is abstraction, implementation should be registered as component', $class);
            throw new MissingDependencyException($message);
        }
        $constructorParams = [];
        if ($reflectedConstructor = $reflectedClass->getConstructor()) {
            $signatureArgs = $reflectedConstructor->getParameters();
            foreach ($signatureArgs as $arg) {
                if ($arg->hasType() && !$arg->getType()->isBuiltin()) {
                    $serviceClassName = $arg->getType()->getName();
                    try {
                        $constructorParams[] = $this->getService($serviceClassName);
                    } catch (MissingDependencyException $e) {
                        if ($this->allowNullableConstructorArgs && $arg->isDefaultValueAvailable()) {
                            $constructorParams[] = $arg->getDefaultValue();
                        } else {
                            $extendedMessage = sprintf('%s [Required in %s constructor]', $e->getMessage(), $class);
                            throw new MissingDependencyException($extendedMessage, 0, $e);
                        }
                    }
                } elseif ($arg->isDefaultValueAvailable()) {
                    $constructorParams[] = $arg->getDefaultValue();
                }
            }
        }

        $instance = new $class(...$constructorParams);

        if ($this->isLoggerInjectionRequired($instance)) {
            if(array_key_exists($class, $this->loggersMap)) {
                $logger = $this->loggersMap[$class];
                if(!$logger instanceof LoggerInterface && is_callable($logger)) {
                    $logger = $logger($this);
                }
            } else {
                $logger = $this->has(LoggerInterface::class) ? $this->getService(LoggerInterface::class) : new NullLogger();
            }

            $instance->setLogger($logger);
        }

        return $instance;
    }

    public function registerService($implementation, ?string $interface = null): void
    {
        $interface = $interface ?: $implementation;
        if (isset($this->serviceMap[$interface])) {
            $oldImplementation = $this->serviceMap[$interface];
            $this->loggerHelper->logRegisterServiceReplacing($implementation, $interface, $oldImplementation);
        }
        $this->serviceMap[$interface] = $implementation;
        $this->loggerHelper->logServiceRegistration($implementation, $interface);
    }

    public function getService(string $type): object
    {
        if (!array_key_exists($type, $this->serviceMap)) {
            if ($this->allowInstantiateNotRegisteredTypes) {
                $this->setService($type, $this->createInstance($type));
            } else {
                $this->loggerHelper->logRequestNotDeterminedService($type);
                throw new MissingDependencyException('Requested service with type ' . $type . ' is not set');
            }
        }

        $service = $this->serviceMap[$type];
        if (is_string($service)) {
            $this->loggerHelper->logLazyLoading($type, $service);
            $service = $this->createInstance($service);
            $this->setService($type, $service);
        } elseif ($service instanceof CallableBeanContainer) {
            $this->loggerHelper->logLazyLoading($type, $service);
            $service = $service();
            $this->setService($type, $service);
        }

        return $service;
    }

    public function hasImplementation(string $interface): bool
    {
        return isset($this->serviceMap[$interface]);
    }

    public function merge(
        array $components,
        string $instancesKey = InjectorBuilder::INSTANCES_KEY,
        string $registerKey = InjectorBuilder::REGISTER_KEY,
        string $callableKey = InjectorBuilder::CALLABLE_KEY
    ): void
    {
        $beansInstances = $components[$instancesKey] ?? [];
        foreach ($beansInstances as $interface => $instance) {
            if (is_int($interface)) {
                $interface = get_class($instance);
            }
            $this->setService($interface, $instance);
        }

        $registeredBeans = $components[$registerKey] ?? [];
        foreach ($registeredBeans as $interface => $implementation) {
            if (is_int($interface)) {
                $interface = $implementation;
            }
            $this->registerService($implementation, $interface);
        }

        $callableBeans = $components[$callableKey] ?? [];
        foreach ($callableBeans as $interface => $callable) {
            $this->registerService(new CallableBeanContainer($interface, $callable, $this), $interface);
        }
    }

    public function allowNullableConstructorArgs(bool $allow): void
    {
        $this->allowNullableConstructorArgs = $allow;
    }

    public function allowInstantiateNotRegisteredTypes(bool $allowInstantiateNotRegisteredTypes): void
    {
        $this->allowInstantiateNotRegisteredTypes = $allowInstantiateNotRegisteredTypes;
    }

    public function registerItSelf(bool $asPsrContainer = true): void
    {
        $this->setService(Injector::class, $this);

        if ($asPsrContainer) {
            $this->setService(ContainerInterface::class, $this);
        }
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        return $this->getService($id);
    }

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        return $this->hasImplementation($id);
    }

    public function useIdAsTypeName(bool $useIdAsTypeName = true): void
    {
        $this->useIdAsTypeName = $useIdAsTypeName;
    }

    public function enableLoggerAwareInjection(bool $enable = true): void
    {
        $this->enableLoggerAwareInjection = $enable;
    }

    public function getLoggersMap(): array
    {
        return $this->loggersMap;
    }

    public function setLoggersMap(array $map): void
    {
        $this->loggersMap = $map;
    }

    private function isLoggerInjectionRequired(object $instance): bool
    {
        return $instance instanceof LoggerAwareInterface && $this->enableLoggerAwareInjection;
    }
}

