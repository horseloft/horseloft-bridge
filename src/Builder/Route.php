<?php

namespace Horseloft\Phalanx\Builder;

use Closure;
use Horseloft\Phalanx\Handler\Container;

class Route
{
    /**
     * @var array
     */
    private static $config = [];

    /**
     * @param string $uri
     * @param string $action
     * @param string|null $namespace
     * @param string ...$interceptor
     */
    public static function get(string $uri, string $action, string $namespace = null, string ...$interceptor)
    {
        Container::setRouterGet(self::routeBuilder([
            'uri' => $uri,
            'action' => $action,
            'namespace' => $namespace,
            'interceptor' => $interceptor
        ]));
    }

    /**
     * @param string $uri
     * @param string $action
     * @param string|null $namespace
     * @param string ...$interceptor
     */
    public static function post(string $uri, string $action, string $namespace = null, string ...$interceptor)
    {
        Container::setRouterPost(self::routeBuilder([
            'uri' => $uri,
            'action' => $action,
            'namespace' => $namespace,
            'interceptor' => $interceptor
        ]));
    }

    /**
     * @param array $config
     * @param Closure $closure
     */
    public static function group(array $config, Closure $closure)
    {
        self::$config = $config;

        $closure(1);

        self::$config = [];
    }

    /**
     * @param array $router
     *
     * @return array[]
     */
    private static function routeBuilder(array $router): array
    {
        // 路由前缀
        $prefix = empty(self::$config['prefix']) ? '/' : '/' . trim(self::$config['prefix'], '/') . '/';

        // 路由组的命名空间
        $configNamespace = empty(self::$config['namespace'])
            ? ''
            : trim(self::$config['namespace'], '\\') . '\\';
        // 路由的命名空间
        $routerNamespace = empty($router['namespace']) ? '' : trim($router['namespace'], '\\') . '\\';

        // 路由组的拦截器
        $configInterceptor = self::getRouterInterceptor(self::$config['interceptor'] ?? []);
        // 路由的拦截器
        $routerInterceptor = self::getRouterInterceptor($router['interceptor'] ?? []);

        // 全部拦截器
        $interceptor = array_merge($configInterceptor, $routerInterceptor);

        // 完整路由的方法的命名空间
        $namespace = Container::getNamespace() . 'Controllers\\' . $configNamespace . $routerNamespace;

        // 路由
        $uri = $prefix . (empty($router['uri']) ? '' : trim($router['uri'], '/'));

        // 路由方法
        $action = $namespace . $router['action'];
        return [
            $uri => ['action' => $action, 'interceptor' => $interceptor]
        ];
    }

    /**
     * @param $interceptor
     *
     * @return array
     */
    private static function getRouterInterceptor($interceptor): array
    {
        if (empty($interceptor)) {
            $response = [];
        } else {
            if (is_array($interceptor)) {
                $response = $interceptor;
            } else {
                $response = [$interceptor];
            }
        }

        $result = [];
        foreach (array_unique($response) as $name) {
            $result[] = ucfirst($name). 'Interceptor';
        }
        return $result;
    }
}
