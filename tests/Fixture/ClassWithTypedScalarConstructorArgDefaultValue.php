<?php

namespace Fixture;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class ClassWithTypedScalarConstructorArgDefaultValue
{
    public const DEFAULT_VALUE = 100500;

    /**
     * @var int
     */
    private $value;
    /**
     * @var array
     */
    private $array;
    /**
     * @var null
     */
    private $notTypedArg;

    public function __construct(int $value = self::DEFAULT_VALUE, array $array = null, $notTypedArg = null)
    {
        $this->value = $value;
        $this->array = $array;
        $this->notTypedArg = $notTypedArg;
    }

    public function getValue()
    {
        return $this->value;
    }
}