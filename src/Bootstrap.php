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

        // 服务基础配置
        $reader->setFramework();

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
        if (!isset($config['cors']) || !is_array($config['cors'])) {
            return;
        }

        // 闭包函数
        $explodeCors = function (string $key) {
            $keys = explode('_', $key);
            return ucfirst(current($keys)) . '-' . ucfirst(end($keys));
        };

        foreach ($config['cors'] as $key => $cors) {
            if ($key == 'allow_origin') {
                $origin = $this->getCorsOrigin($cors);
                if ($origin != '') {
                    header('Access-Control-Allow-Origin:' . $origin);
                }
            } else {
                header('Access-Control-' . $explodeCors($key) . ':' . $cors);
            }
        }
        if (Container::getRequestMethod() == 'OPTIONS') {
            Response::exit();
        }
        return;
    }

    /**
     * 获取要设置的Access-Control-Allow-Origin
     *
     * @param string $allowOrigins
     *
     * @return mixed|string
     */
    private function getCorsOrigin(string $allowOrigins)
    {
        if (strpos($allowOrigins, ',') === false) {
            return $allowOrigins;
        } else {
            $requestHeaders = headers();
            if (isset($requestHeaders['Origin']) && in_array($requestHeaders['Origin'], explode(',', $allowOrigins))) {
                return $requestHeaders['Origin'];
            }
            return '';
        }
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

        // 路由组件
        $router = $this->loopEvent->getActionRouter();

        // 拦截器
        $interceptor = $this->loopEvent->checkInterceptor($router['interceptor']);
        if ($interceptor !== true) {
            Response::exit($interceptor);
        }
        $this->action = $router['action'];
    }
}
