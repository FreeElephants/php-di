<?php

namespace Fixture;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
interface SomeServiceInterface
{
    public function getAnotherService(): AnotherServiceInterface;
}