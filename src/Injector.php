<?php

namespace FreeElephants\DI;

use FreeElephants\DI\Exception\InvalidArgumentException;
use FreeElephants\DI\Exception\OutOfBoundsException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class Injector
{

    private $serviceMap = [];

    private $loggerHelper;
    /**
     * @var bool
     */
    private $allowNullableConstructorArgs = false;

    private $allowInstantiateNotRegisteredTypes = false;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->loggerHelper = new LoggerHelper($logger ?: new NullLogger());
    }

    public function getLoggerHelper(): LoggerHelper
    {
        return $this->loggerHelper;
    }

    public function setService(string $typeName, $service)
    {
        if ($service instanceof $typeName) {
            if (isset($this->serviceMap[$typeName]) && is_object($this->serviceMap[$typeName])) {
                $this->loggerHelper->logServiceInstanceReplacing($typeName, $service, $this->serviceMap[$typeName]);
            }
            $this->serviceMap[$typeName] = $service;
        } else {
            $this->loggerHelper->logNotMatchedTypeInstance($typeName, $service);
            throw new InvalidArgumentException('Given instance not belong to this type. ');
        }
        $this->loggerHelper->logServiceSetting($typeName, $service);
    }

    /**
     * @param $class
     * @return <$class>
     */
    public function createInstance($class)
    {
        $reflectedClass = new \ReflectionClass($class);
        $constructorParams = [];
        if ($reflectedConstructor = $reflectedClass->getConstructor()) {
            $signatureArgs = $reflectedConstructor->getParameters();
            foreach ($signatureArgs as $arg) {
                if ($arg->hasType() && $arg->getClass()) {
                    $serviceClassName = (string)$arg->getType();
                    try {
                        $constructorParams[] = $this->getService($serviceClassName);
                    } catch (OutOfBoundsException $e) {
                        if ($this->allowNullableConstructorArgs && $arg->isDefaultValueAvailable()) {
                            $constructorParams[] = $arg->getDefaultValue();
                        } else {
                            $extendedMessage = sprintf('%s[Required in %s constructor]', $e->getMessage(), $class);
                            throw new OutOfBoundsException($extendedMessage, null, $e);
                        }
                    }
                } elseif ($arg->isDefaultValueAvailable()) {
                    $constructorParams[] = $arg->getDefaultValue();
                }
            }
        }

        return new $class(...$constructorParams);
    }

    public function registerService(string $implementation, string $interface = null)
    {
        $interface = $interface ?: $implementation;
        if (isset($this->serviceMap[$interface])) {
            $oldImplementation = $this->serviceMap[$interface];
            $this->loggerHelper->logRegisterServiceReplacing($implementation, $interface, $oldImplementation);
        }
        $this->serviceMap[$interface] = $implementation;
        $this->loggerHelper->logServiceRegistration($implementation, $interface);
    }

    public function getService(string $type)
    {
        if (!array_key_exists($type, $this->serviceMap)) {
            if ($this->allowInstantiateNotRegisteredTypes) {
                $this->setService($type, $this->createInstance($type));
            } else {
                $this->loggerHelper->logRequestNotDeterminedService($type);
                throw new OutOfBoundsException('Requested service with type ' . $type . ' is not set. ');
            }
        }

        $service = $this->serviceMap[$type];
        if (is_string($service)) {
            $this->loggerHelper->logLazyLoading($type, $service);
            $service = $this->createInstance($service);
            $this->setService($type, $service);
        }

        return $service;
    }

    public function hasImplementation(string $interface): bool
    {
        return isset($this->serviceMap[$interface]);
    }

    public function merge(array $components, string $instancesKey = InjectorBuilder::INSTANCES_KEY, string $registerKey = InjectorBuilder::REGISTER_KEY)
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
    }

    public function allowNullableConstructorArgs(bool $allow)
    {
        $this->allowNullableConstructorArgs = $allow;
    }

    /**
     * @param bool $allowInstantiateNotRegisteredTypes
     */
    public function allowInstantiateNotRegisteredTypes(bool $allowInstantiateNotRegisteredTypes)
    {
        $this->allowInstantiateNotRegisteredTypes = $allowInstantiateNotRegisteredTypes;
    }

    public function registerItSelf()
    {
        $this->setService(Injector::class, $this);
    }
}