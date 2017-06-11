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
        foreach ($beansInstances as $interface => $instance) {
            if (is_int($interface)) {
                $interface = get_class($instance);
            }
            $injector->setService($interface, $instance);
        }
        $registeredBeans = $components[$this->registerKey] ?? [];
        foreach ($registeredBeans as $interface => $implementation) {
            if (is_int($interface)) {
                $interface = $implementation;
            }
            $injector->registerService($implementation, $interface);
        }

        return $injector;
    }
}