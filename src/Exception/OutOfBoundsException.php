<?php

namespace FreeElephants\DI\Exception;

use Psr\Container\NotFoundExceptionInterface;

/**
 * @author samizdam <samizdam@inbox.ru>
 * @internal
 */
class OutOfBoundsException extends \OutOfBoundsException implements ExceptionInterface, NotFoundExceptionInterface
{

}
