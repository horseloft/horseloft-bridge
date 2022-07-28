<?php

namespace Horseloft\Phalanx\Handler;

class Container
{
    /**
     * @var string
     */
    private static $namespace;

    /**
     * @var string
     */
    private static $applicationPath;

    /**
     * @var bool
     */
    private static $debug = false;

    /**
     * @var bool
     */
    private static $requestLog = true;

    /**
     * @var bool
     */
    private static $errorLog = true;

    /**
     * @var bool
     */
    private static $errorLogTrace = true;

    /**
     * @var string
     */
    private static $env = [];

    /**
     * @var string
     */
    private static $logType = 'string';

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
    private static $requestRoute = '';

    /**
     * @var string
     */
    private static $requestUri = '';

    /**
     * @var string
     */
    private static $requestIP = '';

    /**
     * @var string
     */
    private static $requestUserAgent = '';

    /**
     * @var array
     */
    private static $requestInterceptor = [];

    /**
     * @var string
     */
    private static $requestAction = [];

    /**
     * @var array
     */
    private static $requestSession = [];

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
    private static $requestParameter = [];

    /**
     * @var array
     */
    private static $routerGet = [];

    /**
     * @var array
     */
    private static $routerPost = [];

    /**
     * @var array
     */
    private static $interceptor = [];

    /**
     * @var array
     */
    private static $requestLogField = [];

    /**
     * @var array
     */
    private static $requestLogExclude = [];

    /**
     * @return string
     */
    public static function getNamespace(): string
    {
        return self::$namespace;
    }

    /**
     * @param string $namespace
     */
    public static function setNamespace(string $namespace): void
    {
        self::$namespace = $namespace;
    }

    /**
     * @return string
     */
    public static function getApplicationPath(): string
    {
        return self::$applicationPath;
    }

    /**
     * @param string $applicationPath
     */
    public static function setApplicationPath(string $applicationPath): void
    {
        self::$applicationPath = $applicationPath;
    }

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
     * @return bool
     */
    public static function isRequestLog(): bool
    {
        return self::$requestLog;
    }

    /**
     * @param bool $requestLog
     */
    public static function setRequestLog(bool $requestLog): void
    {
        self::$requestLog = $requestLog;
    }

    /**
     * @return bool
     */
    public static function isErrorLog(): bool
    {
        return self::$errorLog;
    }

    /**
     * @param bool $errorLog
     */
    public static function setErrorLog(bool $errorLog): void
    {
        self::$errorLog = $errorLog;
    }

    /**
     * @return bool
     */
    public static function isErrorLogTrace(): bool
    {
        return self::$errorLogTrace;
    }

    /**
     * @param bool $errorLogTrace
     */
    public static function setErrorLogTrace(bool $errorLogTrace): void
    {
        self::$errorLogTrace = $errorLogTrace;
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
    public static function getLogType(): string
    {
        return self::$logType;
    }

    /**
     * @param string $logType
     */
    public static function setLogType(string $logType): void
    {
        self::$logType = $logType;
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
    public static function getRequestRoute(): string
    {
        return self::$requestRoute;
    }

    /**
     * @param string $requestRoute
     */
    public static function setRequestRoute(string $requestRoute): void
    {
        self::$requestRoute = $requestRoute;
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
     * @return string
     */
    public static function getRequestUserAgent(): string
    {
        return self::$requestUserAgent;
    }

    /**
     * @param string $requestUserAgent
     */
    public static function setRequestUserAgent(string $requestUserAgent): void
    {
        self::$requestUserAgent = $requestUserAgent;
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
    public static function getRequestInterceptor(): array
    {
        return self::$requestInterceptor;
    }

    /**
     * @param array $requestInterceptor
     */
    public static function setRequestInterceptor(array $requestInterceptor): void
    {
        self::$requestInterceptor = $requestInterceptor;
    }

    /**
     * @return string
     */
    public static function getRequestAction(): string
    {
        return self::$requestAction;
    }

    /**
     * @param string $requestAction
     */
    public static function setRequestAction(string $requestAction): void
    {
        self::$requestAction = $requestAction;
    }

    /**
     * @return array
     */
    public static function getRequestSession(): array
    {
        return self::$requestSession;
    }

    /**
     * @param array $requestSession
     */
    public static function setRequestSession(array $requestSession): void
    {
        self::$requestSession = $requestSession;
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
    public static function getRequestParameter(): array
    {
        return self::$requestParameter;
    }

    /**
     * @param array $requestParameter
     */
    public static function setRequestParameter(array $requestParameter): void
    {
        self::$requestParameter = array_merge(self::$requestParameter, $requestParameter);
    }

    /**
     * @return array
     */
    public static function getRouterGet(): array
    {
        return self::$routerGet;
    }

    /**
     * @param array $routerGet
     */
    public static function setRouterGet(array $routerGet): void
    {
        self::$routerGet = array_merge(self::$routerGet, $routerGet);
    }

    /**
     * @return array
     */
    public static function getRouterPost(): array
    {
        return self::$routerPost;
    }

    /**
     * @param array $routerPost
     */
    public static function setRouterPost(array $routerPost): void
    {
        self::$routerPost = array_merge(self::$routerPost, $routerPost);
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

    /**
     * @return array
     */
    public static function getRequestLogField(): array
    {
        return self::$requestLogField;
    }

    /**
     * @param array $requestLogField
     */
    public static function setRequestLogField(array $requestLogField): void
    {
        self::$requestLogField = $requestLogField;
    }

    /**
     * @return array
     */
    public static function getRequestLogExclude(): array
    {
        return self::$requestLogExclude;
    }

    /**
     * @param array $requestLogExclude
     */
    public static function setRequestLogExclude(array $requestLogExclude): void
    {
        self::$requestLogExclude = $requestLogExclude;
    }
}
