<?php

namespace Horseloft\Phalanx\Handler;

use Horseloft\Phalanx\Builder\Medium;
use Horseloft\Phalanx\Builder\Request;
use Horseloft\Phalanx\Builder\Response;
use Horseloft\Phalanx\ShutdownException;
use ReflectionClass;
use Throwable;

class Runtime
{
    /**
     * @var string[]
     */
    private static $exceptions = [
        E_ERROR => "E_ERROR",
        E_WARNING => "E_WARNING",
        E_PARSE => "E_PARSE",
        E_NOTICE => "E_NOTICE",
        E_CORE_ERROR => "E_CORE_ERROR",
        E_CORE_WARNING => "E_CORE_WARNING",
        E_COMPILE_ERROR => "E_COMPILE_ERROR",
        E_COMPILE_WARNING => "E_COMPILE_WARNING",
        E_USER_ERROR => "E_USER_ERROR",
        E_USER_WARNING => "E_USER_WARNING",
        E_USER_NOTICE => "E_USER_NOTICE",
        E_STRICT => "E_STRICT",
        E_RECOVERABLE_ERROR => "E_RECOVERABLE_ERROR",
        E_DEPRECATED => "E_DEPRECATED",
        E_USER_DEPRECATED => "E_USER_DEPRECATED",
        E_ALL => "E_ALL"
    ];

    /**
     * 自定义异常处理
     *
     * @param Throwable $e
     *
     * @return void
     */
    public static function exception(Throwable $e)
    {
        try {
            $class = Container::getNamespace() . 'Exceptions\Runtime';
            $response = Medium::reflectionMultiple($class, 'handle', new Request(), $e);
            if (is_null($response)) {
                exit();
            }
            Response::output($response);
        } catch (Throwable $e){
            if (Container::isDebug()) {
                Response::setContentType('text/html; charset=UTF-8');
                Response::output($e->getMessage());
            } else {
                exit();
            }
        }
    }

    /**
     * 进程终止时的自定义处理
     */
    public static function shutdown()
    {
        $e = error_get_last();
        if (is_null($e)) {
            exit();
        }
        $type = array_key_exists($e['type'], self::$exceptions) ? self::$exceptions[$e['type']] . ': ' : '';
        $message = $type . $e['message'] . ' in ' . $e['file'] . ' (' . $e['line'] . ')';
        self::exception(new ShutdownException($message, $e['type']));
    }
}
