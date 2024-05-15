<?php

namespace FreeElephants\DI;

use FreeElephants\DI\Exception\InvalidArgumentException;
use Psr\Container\ContainerInterface;

class CallableBeanContainer
{
    /**
     * @var callable|mixed
     */
    private mixed $function;
    private array $args;

    public function __construct(string $interface, $callable, ContainerInterface $container)
    {
        if (is_callable($callable)) {
            $function = $callable;
            $args = [$container, $interface];
        } elseif(is_array($callable)) {
            $function = array_shift($callable);
            $args = array_merge([$container], $callable);
        } else {
            throw new InvalidArgumentException();
        }

        $this->function = $function;
        $this->args = $args;
    }

    /**
     * @return mixed
     */
    public function __invoke()
    {
        return call_user_func($this->function, ...$this->args);
    }
}
