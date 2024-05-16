<?php

namespace Fixture;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
class AnotherLoggerAwareClass implements LoggerAwareInterface
{

    use LoggerAwareTrait;

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}

