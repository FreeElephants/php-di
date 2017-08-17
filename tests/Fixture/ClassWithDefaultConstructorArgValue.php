<?php


namespace Fixture;


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