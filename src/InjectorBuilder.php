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
    public const LOGGERS_KEY   = 'loggers';

    private string $instancesKey;
    private string $registerKey;
    private string $callableKey;
    private string $loggersKey;

    public function __construct(
        string $instancesKey = self::INSTANCES_KEY,
        string $registerKey = self::REGISTER_KEY,
        string $callableKey = self::CALLABLE_KEY,
        string $loggersKey = self::LOGGERS_KEY,
    )
    {
        $this->instancesKey = $instancesKey;
        $this->registerKey = $registerKey;
        $this->callableKey = $callableKey;
        $this->loggersKey = $loggersKey;
    }

    public function buildFromArray(array $components): Injector
    {
        $injector = new Injector();

        $injector->merge($components, $this->instancesKey, $this->registerKey, $this->callableKey);

        if (isset($components[$this->loggersKey])) {
            $injector->setLoggersMap($components[$this->loggersKey]);
        }

        return $injector;
    }
}
