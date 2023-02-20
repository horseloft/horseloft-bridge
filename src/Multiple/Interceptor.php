<?php

namespace Horseloft\Phalanx\Multiple;

use Horseloft\Phalanx\Builder\Request;

abstract class Interceptor
{
    public function __construct()
    {

    }

    /**
     * 拦截器必须有方法handle 并且handle必须指定参数$request 并且格式为 Horseloft\Core\Drawer\Request
     *
     * 仅当handle()方法的返回值===true时，允许请求通过拦截器
     *
     * handle()方法的返回值不全等于 true, 则handle()方法的返回值将作为本次接口的响应值输出
     *
     * @param Request $request
     * @return mixed
     */
    public abstract function handle(Request $request);
}
