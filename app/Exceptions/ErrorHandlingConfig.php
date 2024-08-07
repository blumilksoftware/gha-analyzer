<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ErrorHandlingConfig extends Exception
{
    public const int HTTP_SESSION_EXPIRED = 419;
    public const array HANDLED_ERROR_CODES = [
        Response::HTTP_FORBIDDEN,
        Response::HTTP_INTERNAL_SERVER_ERROR,
        Response::HTTP_SERVICE_UNAVAILABLE,
        Response::HTTP_NOT_FOUND,
    ];
}
