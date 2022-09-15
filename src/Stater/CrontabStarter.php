<?php

namespace Horseloft\Phalanx\Stater;

use Horseloft\Phalanx\Handler\Crontab;

trait CrontabStarter
{
    /**
     * 定时任务
     *
     * @param array $crontabList
     */
    private function scheduleRun(array $crontabList)
    {
        $minute = -1;
        $isRecovered = false;
        $crontab = new Crontab();
        while (true)
        {
            // 分钟数相同 并且已经运行过
            if ($minute == date('i') && $isRecovered) {
                sleep(1);
                continue;
            }

            $minute = date('i');
            $task = $crontab->runTask($crontabList);
            $isRecovered = true;

            // 没有任务执行，未创建进程
            if (empty($task)) {
                continue;
            }
            // 任务检测
            $crontab->processObserver();
        }
    }
}
