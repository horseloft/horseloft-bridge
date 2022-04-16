<?php

namespace Horseloft\Phalanx\Handler;

use Exception;
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
        $exceptionClassName = (new ReflectionClass($e))->getShortName();
        $namespace = Container::getNamespace() . 'Exceptions\\';

        // 异常处理类中必须有handle方法
        try {
            if (is_callable([$namespace . $exceptionClassName, 'handle'])) {
                $response = call_user_func([$namespace . $exceptionClassName, 'handle'], new Request(), $e);
            } else {
                $response = call_user_func([$namespace . 'Exception', 'handle'], new Request(), $e);
            }
        } catch (Exception $e){
            // 捕捉回调方法中的异常
        }

        $msg = '';
        $message = 'Uncaught Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
        $trace = $message . PHP_EOL . "Stack trace:" . PHP_EOL . $e->getTraceAsString();
        if (Container::isErrorLog() === true) {
            $msg = $message;
        }
        if (Container::isErrorLogTrace() === true) {
            $msg = $trace;
        }
        if ($msg != '') {
            Logger::error($msg);
        }

        // 自定义异常信息
        if (isset($response)) {
            Response::exit($response);
        }

        // debug模式的异常信息输出
        if (Container::isDebug()) {
            Response::echo($trace);
        }
        exit();
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
        $msg = '';
        if (Container::isErrorLog()) {
            $msg = strstr($e['message'], 'Stack trace:', true);
        }
        if (Container::isErrorLogTrace()) {
            $msg = $e['message'];
        }
        // 日志记录
        if ($msg != '') {
            Logger::error($msg);
        }
        // 调试模式
        if (Container::isDebug()) {
            Response::echo($e['message']);
        }
        exit();
    }
}
