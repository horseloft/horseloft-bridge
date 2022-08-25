<?php

namespace Horseloft\Phalanx\Handler;

use Horseloft\Phalanx\Builder\Request;
use Horseloft\Phalanx\Builder\Response;
use ReflectionClass;
use Throwable;

class Runtime
{
    /**
     * 自定义异常处理
     *
     * @param Throwable $e
     *
     * @return void
     */
    public static function exception(Throwable $e)
    {
        error_clear_last();
        $exceptionClassName = (new ReflectionClass($e))->getShortName();
        $namespace = Container::getNamespace() . 'Runtime\\';

        $response = null;
        try {
            // 自定义异常处理
            if (is_callable([$namespace . $exceptionClassName, 'handle'])) {
                $response = call_user_func([$namespace . $exceptionClassName, 'handle'], new Request(), $e);
            } else {
                // 默认异常处理
                $response = call_user_func([$namespace . 'Exception', 'handle'], new Request(), $e);
            }
        } catch (Throwable $e){
            // 捕捉回调方法中的异常
        }
        $message = $exceptionClassName . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
        $trace = $message . PHP_EOL . "Stack trace:" . PHP_EOL . $e->getTraceAsString();

        // 异常信息记录和输出
        self::messageAction($message, $trace, $response);
    }

    /**
     * 进程终止时的自定义处理
     */
    public static function shutdown()
    {
        $e = error_get_last();
        if (!$e) {
            exit();
        }

        $exceptions = [
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
        $exceptionType = isset($exceptions[$e['type']]) ? $exceptions[$e['type']] . ' ' : '';

        if (strpos($e['message'], 'Stack trace:') === false) {
            $message = $exceptionType . $e['message'] . ' in ' . $e['file'] . ':' . $e['line'];
            $trace = $message;
        } else {
            $message = $exceptionType . strstr($e['message'], 'Stack trace:', true);
            $trace = $exceptionType . $e['message'];
        }

        self::messageAction($message, $trace, null);
    }

    /**
     * 错误信息处理
     *
     * @param string $message
     * @param string $trace
     * @param $response
     */
    private static function messageAction(string $message, string $trace, $response)
    {
        $msg = '';
        // 错误信息
        if (Container::isErrorLog()) {
            $msg = $message;
        }

        // 错误追踪信息
        if (Container::isErrorTraceLog()) {
            $msg = $trace;
        }

        // 日志记录
        if ($msg != '') {
            Logger::error($msg);
        }

        // 自定义异常信息输出
        if (!is_null($response)) {
            Response::output($response);
        }

        // 错误输出
        if (Container::isDebug()) {
            Response::setContentType('text/html; charset=UTF-8');
            Response::output($msg);
        }
    }
}
