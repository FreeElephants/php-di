<?php

namespace FreeElephants\DI;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class InjectorBuilder
{
    public const INSTANCES_KEY = 'instances';
    public const REGISTER_KEY  = 'register';
    public const CALLABLE_KEY  = 'callable';
    /**
     * @var string
     */
    private $instancesKey;
    /**
     * @var string
     */
    private $registerKey;
    /**
     * @var string
     */
    private $callableKey;

    public function __construct(
        string $instancesKey = self::INSTANCES_KEY,
        string $registerKey = self::REGISTER_KEY,
        string $callableKey = self::CALLABLE_KEY
    )
    {
        $this->instancesKey = $instancesKey;
        $this->registerKey = $registerKey;
        $this->callableKey = $callableKey;
    }

    public function buildFromArray(array $components): Injector
    {
        $injector = new Injector();

        $injector->merge($components, $this->instancesKey, $this->registerKey, $this->callableKey);

        return $injector;
    }
}
