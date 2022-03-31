<?php

namespace Horseloft\Bridge;

use Throwable;

class BridgeException extends \RuntimeException
{
    public function __construct($message = "", $code = 4004, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}