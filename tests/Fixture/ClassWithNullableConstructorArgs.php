<?php

namespace Fixture;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class ClassWithNullableConstructorArgs
{


    /**
     * @var AnotherServiceInterface
     */
    private $anotherService;

    public function __construct(?AnotherServiceInterface $anotherService = null)
    {
        $this->anotherService = $anotherService ?: new DefaultAnotherServiceImpl();
    }

    public function getAnotherService(): AnotherServiceInterface
    {
        return $this->anotherService;
    }
}

class DefaultAnotherServiceImpl implements AnotherServiceInterface
{

}
