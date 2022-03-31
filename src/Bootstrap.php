<?php

namespace Horseloft\Bridge;

use Horseloft\Bridge\Builder\FileReader;

class Bootstrap
{
    public function __construct(string $root, string $namespace = 'Application')
    {
        $reader = new FileReader($root);

        // 读取Env文件到缓存
        $reader->readAndSetEnv();

        // 读取配置文件到缓存
        $reader->readSetConfig();
    }

    public function run()
    {

    }
}
