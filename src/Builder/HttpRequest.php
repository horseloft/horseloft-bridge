<?php

namespace Horseloft\Phalanx\Builder;

use Horseloft\Phalanx\HorseloftPhalanxException;
use Horseloft\Phalanx\Handler\Container;
use Horseloft\Phalanx\Handler\Logger;

class HttpRequest
{
    public function readSetRequest() : void
    {
        // request session
        if (isset($_SESSION)) {
            Container::setRequestSession($_SESSION);
        }

        // request UA
        Container::setRequestUserAgent($_SERVER['HTTP_USER_AGENT'] ?? []);

        // request header
        Container::setRequestHeader($this->getRequestHeader());

        // request route
        Container::setRequestRoute($this->getRequestRoute());

        // request uri
        Container::setRequestUri($_SERVER['REQUEST_URI']);

        // request method
        Container::setRequestMethod(strtoupper($_SERVER['REQUEST_METHOD']));

        // files
        Container::setRequestFiles($_FILES);

        // cookie
        Container::setRequestCookie($_COOKIE);

        // 请求IP
        Container::setRequestIP($this->getRequestIP());

        // 请求参数 POST+GET+RAW
        Container::setRequestParameter($this->getParameter());
    }

    /**
     * @return array
     */
    public function getRequestRouter(): array
    {
        if (Container::getRequestMethod() === 'GET') {
            $router = Container::getRouterGet();
        } else {
            $router = Container::getRouterPost();
        }
        return $router;
    }

    /**
     * @param array $requestInterceptor
     *
     * @return bool|mixed
     */
    public function checkInterceptor(array $requestInterceptor)
    {
        Container::setRequestInterceptor($requestInterceptor);
        $allInterceptor = Container::getInterceptor();
        // 拦截器验证
        foreach ($requestInterceptor as $interceptor) {
            if (!isset($allInterceptor[$interceptor])) {
                throw new HorseloftPhalanxException($interceptor . ' Not Found');
            }
            if (!is_callable($allInterceptor[$interceptor])) {
                throw new HorseloftPhalanxException($interceptor . ' Is Not Callable');
            }
            $response = call_user_func($allInterceptor[$interceptor], new Request());
            if ($response !== true) {
                return Response::return($response);
            }
        }
        return true;
    }

    /**
     * @param string $action
     */
    public function getActionResponse(string $action)
    {
        Container::setRequestAction($action);
        // action验证
        if (!is_callable($action)) {
            throw new HorseloftPhalanxException('Action Is Not Callable');
        }

        // 执行请求并输出结果
        Response::exit(call_user_func($action, new Request()));
    }

    /**
     * 请求信息写入日志
     */
    public function requestLogRecord()
    {
        // 仅当 request_log === true 记录日志
        $envData = Container::getEnv();
        if (isset($envData['request_log']) && $envData['request_log'] === false) {
            return;
        }
        $message = 'IP=' . Container::getRequestIP() .
            '; Route=' . Container::getRequestRoute() .
            '; Method=' . Container::getRequestMethod() .
            '; Parameters=' . json_encode(Container::getRequestParameter()) .
            '; User-Agent=' . Container::getRequestUserAgent();
        Logger::info($message);
    }

    /**
     * @return array
     */
    private function getParameter(): array
    {
        $parameter = $_REQUEST;
        $content = file_get_contents('php://input');
        if (!empty($content)) {
            $json = json_decode(file_get_contents('php://input'), true);
            if (!empty($json)) {
                $parameter = array_merge($parameter, $json);
            }
        }
        return $parameter;
    }

    /**
     * @return string
     */
    private function getRequestIP(): string
    {
        $requestIP = $_SERVER['x-forwarded-for'] ?? $_SERVER['x-real-ip'] ?? $_SERVER['REMOTE_ADDR'];
        //如果是代理转发，IP为逗号分隔的字符串
        if (strpos($requestIP, ',')) {
            $address = explode(',', $requestIP);
            $requestIP = end($address);
        }
        return $requestIP;
    }

    /**
     * @return string
     */
    private function getRequestRoute(): string
    {
        if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
            $route = strstr($_SERVER['REQUEST_URI'], '?', true);
        } else {
            $route = $_SERVER['REQUEST_URI'] == '/' ? '/' : rtrim($_SERVER['REQUEST_URI'], '/');
        }
        return $route;
    }

    /**
     * @return array
     */
    private function getRequestHeader(): array
    {
        $headers = getallheaders();
        return $headers === false ? [] : $headers;
    }
}