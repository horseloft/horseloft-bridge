<?php

namespace Horseloft\Phalanx;

use Throwable;
use RuntimeException;

class FileNotFoundException extends RuntimeException
{
    public function __construct($message = "", $code = 4003, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
