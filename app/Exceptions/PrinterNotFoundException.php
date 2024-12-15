<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class PrinterNotFoundException extends Exception
{
    public function __construct($message = 'Printer Not Found', $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
