<?php

namespace FreeElephants\DI;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class InjectorBuilder
{
    const INSTANCES_KEY = 'instances';
    const REGISTER_KEY = 'register';
    /**
     * @var string
     */
    private $instancesKey;
    /**
     * @var string
     */
    private $registerKey;

    public function __construct(string $instancesKey = self::INSTANCES_KEY, string $registerKey = self::REGISTER_KEY)
    {
        $this->instancesKey = $instancesKey;
        $this->registerKey = $registerKey;
    }

    public function buildFromArray(array $components): Injector
    {
        $injector = new Injector();
        $beansInstances = $components[$this->instancesKey] ?? [];
        foreach ($beansInstances as $name => $instance) {
            $injector->setService($name, $instance);
        }
        $registeredBeans = $components[$this->registerKey] ?? [];
        foreach ($registeredBeans as $interface => $implementation) {
            $injector->registerService($implementation, $interface);
        }

        return $injector;
    }
}