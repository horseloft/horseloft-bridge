<?php

namespace Horseloft\Phalanx;

use Throwable;

class HorseloftPhalanxException extends \RuntimeException
{
    public function __construct($message = "", $code = 4004, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}