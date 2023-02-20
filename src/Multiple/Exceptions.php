<?php

namespace Horseloft\Phalanx\Multiple;

use Horseloft\Phalanx\Builder\Request;
use Throwable;

abstract class Exceptions
{
    public function __construct()
    {

    }

    /**
     * 默认异常处理类
     *
     * 当前类中必须存在handle()方法
     *
     * handle()方法的第一个参数：Horseloft\Bridge\Builder\Request $request
     *
     * handle()方法的第二个参数：\Throwable $e
     *
     * handle()方法的返回值将作为本次接口的响应值输出，返回值为Null则不输出任何内容
     *
     * @param Request $request
     * @param Throwable $e
     *
     * @return mixed
     */
    public function handle(Request $request, Throwable $e)
    {
        return null;
    }
}
