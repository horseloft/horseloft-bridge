<?php

namespace Horseloft\Phalanx;

use Horseloft\Phalanx\Builder\LoopEvent;
use Horseloft\Phalanx\Builder\Request;
use Horseloft\Phalanx\Handler\Container;
use Horseloft\Phalanx\Stater\CommandStater;
use Horseloft\Phalanx\Stater\CrontabStarter;
use Horseloft\Phalanx\Stater\HttpStater;

class Bootstrap
{
    use HttpStater,CommandStater,CrontabStarter;

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
        $this->scheduleRun($crontabList);
    }
}
