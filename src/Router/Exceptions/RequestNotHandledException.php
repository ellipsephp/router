<?php declare(strict_types=1);

namespace Ellipse\Router\Exceptions;

use RuntimeException;

class RequestNotHandledException extends RuntimeException implements RouterExceptionInterface
{
    public function __construct()
    {
        parent::__construct('The router must handle the request before a match name can be retrieved.');
    }
}
