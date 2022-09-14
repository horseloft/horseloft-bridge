<?php

namespace Horseloft\Phalanx;

use Horseloft\Phalanx\Builder\FileReader;
use Horseloft\Phalanx\Builder\LoopEvent;
use Horseloft\Phalanx\Builder\Request;
use Horseloft\Phalanx\Builder\Response;
use Horseloft\Phalanx\Handler\Container;
use Horseloft\Phalanx\Handler\Crontab;
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
     * @param bool $isCommand
     * @throws \ReflectionException
     */
    public function __construct(string $root, string $namespace = 'Application', bool $isCommand = false)
    {
        $this->registerErrorAndException();

        $this->registerFileCache($root, $namespace, $isCommand);
    }

    /**
     * 启动HTTP请求请求并输出结果
     */
    public function run()
    {
        $this->registerLoopEvent();

        $this->corsHandler();

        $this->loopEventAction();

        $this->loopEvent->getActionResponse($this->action);
    }

    /**
     * 执行命令行操作
     */
    public function command()
    {
        $callable = $this->getCommandCallable($_SERVER['argv']);
        $argument = $this->getCommandArgument($_SERVER['argv']);

        Container::setRequestParameter($argument);

        call_user_func($callable, new Request());
    }

    /**
     * 启动定时任务
     *
     * 注意：
     *  定时任务列表中的全部任务同时执行
     *  定时任务列表中的全部任务执行结束，才会进入下一次的任务循环
     */
    public function crontab()
    {
        $crontabList = config('crontab', []);
        if (empty($crontabList)) {
            return;
        }
        $observer = true;
        $runningWorker = [];
        $crontabHandle = new Crontab();

        while (true)
        {
            sleep(1);
            if (date('s') > $crontabHandle->enableTime) {
                $runningWorker = []; // 运行中的任务
                $observer = true; // 允许进程检测
                continue;
            }
            foreach ($crontabList as $name => $crontab) {
                // 本次已执行过
                if (array_key_exists($name, $runningWorker) || $crontabHandle->isInvalidCrontab($crontab)) {
                    break;
                }

                // 未到定时任务的执行时间
                $timeCommand = $crontabHandle->commandResolve($crontab['command']);
                if (empty($timeCommand) || $crontabHandle->isInvalidRunTime($timeCommand)) {
                    continue;
                }

                // 创建进程并执行任务
                $runningWorker[$name] = $name;
                $args = empty($crontab['args']) ? [] : $crontab['args'];
                $crontabHandle->createProcess($name, $crontab['callback'], $args);
            }

            // 没有任务执行，未创建进程
            if (empty($runningWorker)) {
                continue;
            }

            // 任务检测
            if ($observer) {
                $observer = false;
                $crontabHandle->processObserver();
            }
        }
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
     * @param $isCommand
     * @throws \ReflectionException
     */
    private function registerFileCache($root, $namespace, $isCommand)
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
        $reader->readAndSetRoute($isCommand);

        // 读取拦截器到缓存
        $reader->readSetInterceptor($isCommand);
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
            exit();
        }
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
            Response::output($interceptor);
        }
        $this->action = $router['action'];
    }

    /**
     * 命令行参数校验
     *
     * @param array $args
     * @return callable|void
     */
    private function getCommandCallable(array $args)
    {
        if (!isset($args[1])) {
            die('命令command缺少可执行参数');
        }
        $commandList = config('command', []);
        if (empty($commandList)) {
            die('command配置缺失');
        }
        if (!isset($commandList[$args[1]])) {
            die('命令command ' . $args[1] . ' 不存在');
        }
        if (!is_callable($commandList[$args[1]])) {
            die('命令command ' . $args[1] . ' 不是一个有效的回调方法');
        }
        return $commandList[$args[1]];
    }

    /**
     * 获取命令行参数
     *
     * 参考格式：php command xxx name=tom,lily age=12 other=old=new
     *
     * 参数结果：[
     *  name => tom,lily,
     *  age => 12,
     *  other => old=new
     * ]
     *
     * @param array $args
     * @return array
     */
    private function getCommandArgument(array $args): array
    {
        $argument = [];
        $params = array_slice($args, 2);
        foreach ($params as $param) {
            $key = mb_strstr($param, '=', true);
            if ($key == false) {
                continue;
            }
            $argument[$key] = ltrim(mb_strstr($param, '='), '=');
        }
        return $argument;
    }
}
