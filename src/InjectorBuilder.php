<?php

namespace FreeElephants\DI;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class InjectorBuilder
{
    public const INSTANCES_KEY = 'instances';
    public const REGISTER_KEY = 'register';
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

        $injector->merge($components, $this->instancesKey, $this->registerKey);

        return $injector;
    }
}