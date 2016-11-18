<?php

namespace Fixture;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class SomeService implements SomeServiceInterface
{
    /**
     * @var AnotherServiceInterface
     */
    private $anotherService;

    public function __construct(AnotherServiceInterface $anotherService)
    {
        $this->anotherService = $anotherService;
    }

    public function getAnotherService(): AnotherServiceInterface
    {
        return $this->anotherService;
    }
}