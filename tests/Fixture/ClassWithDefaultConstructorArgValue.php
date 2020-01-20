<?php

namespace Fixture;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class ClassWithDefaultConstructorArgValue
{

    /**
     * @var int
     */
    private $value;

    public function __construct($value = 100500)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}