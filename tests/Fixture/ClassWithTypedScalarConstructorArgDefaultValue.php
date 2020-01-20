<?php

namespace Fixture;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class ClassWithTypedScalarConstructorArgDefaultValue
{

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

    public function __construct(int $value = 100500, array $array = null, $notTypedArg = null)
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