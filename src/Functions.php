<?php

use Isobaric\Phalanx\Handler\Container;

if (!function_exists('env')) {
    /**
     * 获取env.ini配置项的值
     *
     * @param string $name
     * @param mixed $default
     * @return false|mixed
     */
    function env(string $name = 'env', $default = false)
    {
        return Container::getEnv()[$name] ?? $default;
    }
}

if (!function_exists('config')) {
    /**
     * --------------------------------------------------------------------------
     *  获取Config中配置的数据信息
     * --------------------------------------------------------------------------
     *
     * 如果未能读取到$name的配置信息，返回$default
     *
     * @param string $name
     * @param mixed $default
     * @return false|mixed
     */
    function config(string $name, $default = false)
    {
        if (strlen($name) == 0) {
            return $default;
        }
        $list = explode('.', $name);

        $var = Container::getConfig();
        foreach ($list as $value) {
            if (!isset($var[$value])) {
                return $default;
            }
            $var = $var[$value];
        }
        return $var;
    }
}

if (!function_exists('root_path')) {
    /**
     * ------------------------------------------------------
     * 项目根目录
     * ------------------------------------------------------
     * 项目目录的绝对路径，末尾有斜线
     *
     * @return string
     */
    function root_path(): string
    {
        return Container::getApplicationPath();
    }
}

if (!function_exists('log_path')) {
    /**
     * ------------------------------------------------------
     * log存储目录
     * ------------------------------------------------------
     * log目录的绝对路径，末尾有斜线
     *
     * @return string
     */
    function log_path(): string
    {
        return Container::getLogPath();
    }
}

if (!function_exists('config_path')) {
    /**
     * ------------------------------------------------------
     * config文件目录
     * ------------------------------------------------------
     * config文件目录的绝对路径，末尾有斜线
     *
     * @return string
     */
    function config_path(): string
    {
        return Container::getConfigPath();
    }
}

if (!function_exists('route')) {
    /**
     * ------------------------------------------------------
     * 当前请求的完整路由
     * ------------------------------------------------------
     *
     * @return string
     */
    function route(): string
    {
        return Container::getRequestRoute();
    }
}

if (!function_exists('method')) {
    /**
     * ------------------------------------------------------
     * 当前请求指向的类方法
     * ------------------------------------------------------
     * 例：GET POST
     *
     * @return string
     */
    function method(): string
    {
        return Container::getRequestMethod();
    }
}

if (!function_exists('action')) {
    /**
     * ------------------------------------------------------
     * 当前请求指向的类方法
     * ------------------------------------------------------
     *
     * @return callable
     */
    function action(): callable
    {
        return Container::getRequestAction();
    }
}

if (!function_exists('interceptor')) {
    /**
     * ------------------------------------------------------
     * 当前请求使用的拦截器名称
     * ------------------------------------------------------
     *
     * @return array
     */
    function interceptor(): array
    {
        return Container::getRequestInterceptor();
    }
}

if (!function_exists('cookies')) {
    /**
     * ------------------------------------------------------
     * 当前请求的cookie
     * ------------------------------------------------------
     *
     * @return array
     */
    function cookies(): array
    {
        return Container::getRequestCookie();
    }
}

if (!function_exists('sessions')) {
    /**
     * ------------------------------------------------------
     * 当前请求的session
     * ------------------------------------------------------
     *
     * @return array
     */
    function sessions(): array
    {
        return Container::getRequestSession();
    }
}

if (!function_exists('headers')) {
    /**
     * ------------------------------------------------------
     * 当前请求的header
     * ------------------------------------------------------
     *
     * @return array
     */
    function headers(): array
    {
        return Container::getRequestHeader();
    }
}
