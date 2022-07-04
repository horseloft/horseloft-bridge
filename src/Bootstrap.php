<?php

namespace Horseloft\Phalanx;

use Horseloft\Phalanx\Builder\FileReader;
use Horseloft\Phalanx\Builder\LoopEvent;
use Horseloft\Phalanx\Builder\Response;
use Horseloft\Phalanx\Handler\Container;
use Horseloft\Phalanx\Handler\Runtime;

class Bootstrap
{
    /**
     * @var LoopEvent
     */
    private $loopEvent;

    /**
     * @var string
     */
    private $action;

    /**
     * @param string $root
     * @param string $namespace
     */
    public function __construct(string $root, string $namespace = 'Application')
    {
        $this->registerErrorAndException();

        $this->registerFileCache($root, $namespace);

        $this->registerLoopEvent();

        $this->corsHandler();

        $this->loopEventAction();
    }

    /**
     * 执行请求并输出结果
     */
    public function run()
    {
        $this->loopEvent->getActionResponse($this->action);
    }

    /**
     * 异常和错误处理并记录错误日志
     */
    private function registerErrorAndException()
    {
        error_reporting(0);

        // 异常处理
        set_exception_handler([Runtime::class, 'exception']);

        // 异常、错误处理程序 | 用于默认日志记录
        register_shutdown_function([Runtime::class, 'shutdown']);
    }

    /**
     * ENV文件、配置文件、路由、拦截器加载
     *
     * @param $root
     * @param $namespace
     */
    private function registerFileCache($root, $namespace)
    {
        // 文件处理
        $reader = new FileReader($root, $namespace);

        // 读取Env文件到缓存
        $reader->readAndSetEnv();

        // 读取配置文件到缓存
        $reader->readSetConfig();

        // 读取路由文件到缓存
        $reader->readAndSetRoute();

        // 读取拦截器到缓存
        $reader->readSetInterceptor();
    }

    /**
     * CORS | OPTIONS
     */
    private function corsHandler()
    {
        $config = Container::getConfig();
        if (!isset($config['cors']) || !is_array($config['cors']) || !isset($config['cors']['allow_origin'])) {
            return;
        }
        header('Access-Control-Allow-Origin:' . $config['cors']['allow_origin']);

        if (Container::getRequestMethod() != 'OPTIONS') {
            return;
        }
        // 闭包函数
        $explodeCors = function (string $key) {
            $keys = explode('_', $key);
            return ucfirst(current($keys)) . '-' . ucfirst(end($keys));
        };
        foreach ($config['cors'] as $key => $cors) {
            if ($key == 'allow_origin') {
                continue;
            }
            header('Access-Control-' . $explodeCors($key) . ':' . $cors);
        }
        Response::exit();
    }

    /**
     * request信息加载
     */
    private function registerLoopEvent()
    {
        $this->loopEvent = new LoopEvent();
        $this->loopEvent->readSetRequest();
    }

    /**
     * 请求信息验证处理
     */
    private function loopEventAction()
    {
        // 请求记录
        $this->loopEvent->requestLogRecord();

        // 请求路由
        $actionUri = $this->loopEvent->getActionUri();

        // 路由组件
        $router = $this->loopEvent->getActionRouter($actionUri);

        // 拦截器
        $interceptor = $this->loopEvent->checkInterceptor($router['interceptor']);
        if ($interceptor !== true) {
            Response::exit($interceptor);
        }
        $this->action = $router['action'];
    }
}
