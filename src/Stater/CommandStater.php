<?php

namespace Horseloft\Phalanx\Stater;

trait CommandStater
{
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
