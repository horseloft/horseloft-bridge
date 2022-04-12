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
     * @var FileReader
     */
    private $reader;

    public function __construct(string $root, string $namespace = 'Application')
    {
        error_reporting(0);

        $this->reader = new FileReader($root, $namespace);

        $this->http = new HttpRequest();

        $this->initialize();
    }

    public function run()
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

        // 输出action结果
        $this->http->getActionResponse($router[$uri]['action']);
    }

    private function initialize()
    {
        // 异常处理
        set_exception_handler([Runtime::class, 'exception']);

        // 异常、错误处理程序 | 用于默认日志记录
        register_shutdown_function([Runtime::class, 'shutdown']);

        // 读取Env文件到缓存
        $this->reader->readAndSetEnv();

        // 请求数据记录到缓存
        $this->http->readSetRequest();

        // 读取配置文件到缓存
        $this->reader->readSetConfig();

        // 读取路由文件到缓存
        $this->reader->readAndSetRoute();

        // 读取拦截器到缓存
        $this->reader->readSetInterceptor();
    }
}
