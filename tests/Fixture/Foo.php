<?php

namespace FreeElephants\DI\Fixture;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class Foo
{

    /**
     * @var Bar
     */
    private $bar;

    public function __construct(Bar $bar)
    {
        $this->bar = $bar;
    }

    public function getBar(): Bar
    {
        return $this->bar;
    }
}