<?php

namespace Horseloft\Phalanx;

use Horseloft\Phalanx\Builder\FileReader;
use Horseloft\Phalanx\Builder\HttpRequest;
use Horseloft\Phalanx\Builder\Response;
use Horseloft\Phalanx\Handler\Container;
use Horseloft\Phalanx\Handler\Runtime;

class Bootstrap
{
    /**
     * @var HttpRequest
     */
    private $http;

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

        $this->registerRequest();

        $this->corsHandler();

        $this->requestHandler();
    }

    /**
     * 执行请求并输出结果
     */
    public function run()
    {
        $this->http->getActionResponse($this->action);
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
     * request信息加载
     */
    private function registerRequest()
    {
        $this->http = new HttpRequest();
        $this->http->readSetRequest();
    }

    /**
     * CORS | OPTIONS
     */
    private function corsHandler()
    {
        if (Container::getRequestMethod() != 'OPTIONS') {
            return;
        }
        $config = Container::getConfig();
        if (!isset($config['cors']) || !is_array($config['cors'])) {
            return;
        }

        foreach ($config['cors'] as $cors) {
            header($cors);
        }
        Response::exit();
    }

    /**
     * 请求信息验证处理
     */
    private function requestHandler()
    {
        // 日志记录
        $this->http->requestLogRecord();

        // 路由验证
        $uri = Container::getRequestRoute();
        $router = $this->http->getRequestRouter();
        if (!isset($router[$uri])) {
            throw new HorseloftPhalanxException('Request Not Found');
        }

        // 拦截器验证
        $interceptor = $this->http->checkInterceptor($router[$uri]['interceptor']);
        if ($interceptor !== true) {
            Response::exit($interceptor);
        }
        $this->action = $router[$uri]['action'];
    }
}
