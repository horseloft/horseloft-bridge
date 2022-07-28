<?php

namespace Horseloft\Phalanx;

use Throwable;

class PhalanxException extends \RuntimeException
{
    public function __construct($message = "", $code = 4001, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}