<?php

namespace Isobaric\Phalanx\Builder;

use Closure;
use Isobaric\Phalanx\Handler\Container;

class Route
{
    /**
     * @var array
     */
    private static $config = [];

    /**
     * @param string $uri
     * @param callable $action
     * @param string ...$interceptor
     */
    public static function get(string $uri, callable $action, string ...$interceptor)
    {
        Container::setRouterGet(self::routeBuilder([
            'uri' => $uri,
            'action' => $action,
            'interceptor' => $interceptor
        ]));
    }

    /**
     * @param string $uri
     * @param callable $action
     * @param string ...$interceptor
     */
    public static function post(string $uri, callable $action, string ...$interceptor)
    {
        Container::setRouterPost(self::routeBuilder([
            'uri' => $uri,
            'action' => $action,
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

        $closure();

        self::$config = [];
    }

    /**
     * @param array $router
     *
     * @return array[]
     */
    private static function routeBuilder(array $router): array
    {
        // 路由组的拦截器
        $configInterceptor = self::getRouterInterceptor(self::$config['interceptor'] ?? []);
        // 路由的拦截器
        $routerInterceptor = self::getRouterInterceptor($router['interceptor'] ?? []);
        // 全部拦截器
        $interceptor = array_merge($configInterceptor, $routerInterceptor);
        // 路由|路由前缀
        $routerPrefix = empty(self::$config['prefix']) ? '' : trim(self::$config['prefix'], '/');
        $routerUri = empty($router['uri']) ? '' : trim($router['uri'], '/');
        $uri = trim($routerPrefix . '/' . $routerUri, '/');

        return [
            $uri => ['action' => $router['action'], 'interceptor' => $interceptor]
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
            $result[] = $name;
        }
        return $result;
    }
}
