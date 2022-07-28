<?php

namespace Horseloft\Phalanx;

use RuntimeException;
use Throwable;

class RequestNotFoundException extends RuntimeException
{
    public function __construct($message = "", $code = 4004, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
