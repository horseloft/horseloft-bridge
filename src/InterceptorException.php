<?php

namespace Horseloft\Phalanx;

use Throwable;
use RuntimeException;

class InterceptorException extends RuntimeException
{
    public function __construct($message = "", $code = 4002, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
