<?php

namespace Horseloft\Phalanx\Builder;

use Horseloft\Phalanx\Handler\Container;
use Horseloft\Phalanx\Handler\Logger;
use Horseloft\Phalanx\InterceptorException;
use Horseloft\Phalanx\PhalanxException;
use Horseloft\Phalanx\RequestNotFoundException;

class LoopEvent
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
     * 当前请求的路由组件
     *
     * @return array
     */
    public function getActionRouter(): array
    {
        if (Container::getRequestMethod() === 'GET') {
            $router = Container::getRouterGet();
        } else {
            $router = Container::getRouterPost();
        }

        $uri = Container::getRequestRoute();
        if (isset($router[$uri])) {
            return $router[$uri];
        }

        $uri = trim($uri, '/');
        if (isset($router[$uri])) {
            return $router[$uri];
        }
        throw new RequestNotFoundException('Request [' . $uri . '] Not Found');
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
                throw new InterceptorException($interceptor . ' Not Found');
            }
            if (!is_callable($allInterceptor[$interceptor])) {
                throw new InterceptorException($interceptor . ' Is Not Callable');
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
            throw new PhalanxException('Action Not Found');
        }

        // 执行请求并输出结果
        Response::exit(call_user_func($action, new Request()));
    }

    /**
     * 请求信息写入日志
     */
    public function requestLogRecord()
    {
        $fields = Container::getRequestLogField();
        if (Container::isRequestLog() === false || empty($fields)) {
            return;
        }
        $exclude = Container::getRequestLogExclude();
        $message = 'date=' . date('Y-m-d H:i:s') . '; ' .
            'ip=' . Container::getRequestIP() .
            '; route=' . Container::getRequestRoute() .
            '; method=' . Container::getRequestMethod();

        foreach ($fields as $field) {
            switch($field) {
                case 'parameter':
                    $message .= '; parameter=' . $this->requestFilter($exclude, 'parameter', Container::getRequestParameter());
                    break;
                case 'session':
                    $message .= '; session=' . $this->requestFilter($exclude, 'session', Container::getRequestSession());
                    break;
                case 'cookie':
                    $message .= '; cookie=' . $this->requestFilter($exclude, 'cookie', Container::getRequestCookie());
                    break;
                case 'header':
                    $message .= '; header=' . $this->requestFilter($exclude, 'header', Container::getRequestHeader());
                    break;
            }
        }
        $message .= '; user-agent=' . Container::getRequestUserAgent();
        Logger::info($message);
    }

    /**
     * 日志参数过滤
     *
     * @param array $exclude
     * @param string $field
     * @param array $params
     * @return string
     */
    private function requestFilter(array $exclude, string $field, array $params): string
    {
        if (isset($exclude[$field]) && !empty($exclude[$field]) && is_array($exclude[$field])) {
            foreach ($params as $key => $param) {
                if (in_array($key, $exclude[$field])) {
                    unset($params[$key]);
                }
            }
        }
        return json_encode($params);
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
