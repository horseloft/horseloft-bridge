<?php

namespace Horseloft\Phalanx;

use RuntimeException;
use Throwable;

class ShutdownException extends RuntimeException
{
    public function __construct($message = "", $code = 4005, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
