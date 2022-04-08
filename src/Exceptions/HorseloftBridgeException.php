<?php

namespace Horseloft\Bridge\Exceptions;

use Throwable;

class HorseloftBridgeException extends \RuntimeException
{
    public function __construct($message = "", $code = 4004, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}