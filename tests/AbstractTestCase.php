<?php

namespace FreeElephants\DI;

use PHPUnit\Framework\TestCase;

/**
 * @author samizdam <samizdam@inbox.ru>
 */
abstract class AbstractTestCase extends TestCase
{
    protected const FIXTURE_PATH = __DIR__ . '/Fixture';
}
