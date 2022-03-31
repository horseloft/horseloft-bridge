<?php

namespace Horseloft\Bridge\Handler;

class Container
{
    /**
     * @var bool
     */
    private static $debug = false;

    /**
     * @var string
     */
    private static $env = [];

    /**
     * @var string
     */
    private static $logPath;

    /**
     * @var string
     */
    private static $logFilename = 'horseloft.log';

    /**
     * @var string
     */
    private static $configPath;

    /**
     * @var array
     */
    private static $config = [];

    /**
     * @var string
     */
    private static $requestMethod = '';

    /**
     * @var string
     */
    private static $requestUri = '';

    /**
     * @var string
     */
    private static $requestIP = '';

    /**
     * @var array
     */
    private static $requestHeader = [];

    /**
     * @var array
     */
    private static $requestCookie = [];

    /**
     * @var array
     */
    private static $requestFiles = [];

    /**
     * @var array
     */
    private static $route = [];

    /**
     * @var array
     */
    private static $interceptor = [];

    /**
     * @return bool
     */
    public static function isDebug(): bool
    {
        return self::$debug;
    }

    /**
     * @param bool $debug
     */
    public static function setDebug(bool $debug): void
    {
        self::$debug = $debug;
    }

    /**
     * @return array
     */
    public static function getEnv(): array
    {
        return self::$env;
    }

    /**
     * @param array $env
     */
    public static function setEnv(array $env): void
    {
        self::$env = $env;
    }

    /**
     * @return string
     */
    public static function getLogPath(): string
    {
        return self::$logPath;
    }

    /**
     * @param string $logPath
     */
    public static function setLogPath(string $logPath): void
    {
        self::$logPath = $logPath;
    }

    /**
     * @return string
     */
    public static function getLogFilename(): string
    {
        return self::$logFilename;
    }

    /**
     * @param string $logFilename
     */
    public static function setLogFilename(string $logFilename): void
    {
        self::$logFilename = $logFilename;
    }

    /**
     * @return string
     */
    public static function getConfigPath(): string
    {
        return self::$configPath;
    }

    /**
     * @param string $configPath
     */
    public static function setConfigPath(string $configPath): void
    {
        self::$configPath = $configPath;
    }

    /**
     * @return array
     */
    public static function getConfig(): array
    {
        return self::$config;
    }

    /**
     * @param string $name
     * @param array $config
     */
    public static function setConfig(string $name, array $config): void
    {
        self::$config[$name] = $config;
    }

    /**
     * @return string
     */
    public static function getRequestMethod(): string
    {
        return self::$requestMethod;
    }

    /**
     * @param string $requestMethod
     */
    public static function setRequestMethod(string $requestMethod): void
    {
        self::$requestMethod = $requestMethod;
    }

    /**
     * @return string
     */
    public static function getRequestUri(): string
    {
        return self::$requestUri;
    }

    /**
     * @param string $requestUri
     */
    public static function setRequestUri(string $requestUri): void
    {
        self::$requestUri = $requestUri;
    }

    /**
     * @return string
     */
    public static function getRequestIP(): string
    {
        return self::$requestIP;
    }

    /**
     * @param string $requestIP
     */
    public static function setRequestIP(string $requestIP): void
    {
        self::$requestIP = $requestIP;
    }

    /**
     * @return array
     */
    public static function getRequestHeader(): array
    {
        return self::$requestHeader;
    }

    /**
     * @param array $requestHeader
     */
    public static function setRequestHeader(array $requestHeader): void
    {
        self::$requestHeader = $requestHeader;
    }

    /**
     * @return array
     */
    public static function getRequestCookie(): array
    {
        return self::$requestCookie;
    }

    /**
     * @param array $requestCookie
     */
    public static function setRequestCookie(array $requestCookie): void
    {
        self::$requestCookie = $requestCookie;
    }

    /**
     * @return array
     */
    public static function getRequestFiles(): array
    {
        return self::$requestFiles;
    }

    /**
     * @param array $requestFiles
     */
    public static function setRequestFiles(array $requestFiles): void
    {
        self::$requestFiles = $requestFiles;
    }

    /**
     * @return array
     */
    public static function getRoute(): array
    {
        return self::$route;
    }

    /**
     * @param array $route
     */
    public static function setRoute(array $route): void
    {
        self::$route = $route;
    }

    /**
     * @return array
     */
    public static function getInterceptor(): array
    {
        return self::$interceptor;
    }

    /**
     * @param array $interceptor
     */
    public static function setInterceptor(array $interceptor): void
    {
        self::$interceptor = $interceptor;
    }
}
