<?php

namespace FreeElephants\DI;

use FreeElephants\DI\Exception\InvalidArgumentException;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class Injector
{

    private $serviceMap = [];

    public function setService(string $typeName, $service)
    {
        if ($service instanceof $typeName) {
            $this->serviceMap[$typeName] = $service;
        } else {
            throw new InvalidArgumentException('Given instance not belong to this type. ');
        }
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
                if ($arg->hasType()) {
                    $serviceClassName = (string)$arg->getType();
                    $constructorParams[] = $this->serviceMap[$serviceClassName];
                } elseif ($arg->isDefaultValueAvailable()) {
                    $constructorParams[] = $arg->getDefaultValue();
                }
            }
        }

        return new $class(...$constructorParams);
    }
}