<?php

namespace Isobaric\Phalanx\Builder;

use Isobaric\Phalanx\Handler\Container;

class Request
{
    /**
     * 添加请求参数
     *
     * @param string $name
     * @param $value
     */
    public function set(string $name, $value) : void
    {
        Container::setRequestParameter([$name => $value]);
    }

    /**
     * 获取指定的请求参数
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get(string $name, $default = '')
    {
        return self::all()[$name] ?? $default;
    }

    /**
     * 获取全部请求参数
     *
     * @return array
     */
    public function all(): array
    {
        return Container::getRequestParameter();
    }

    /**
     * 获取header
     *
     * @param string $name
     * @return string
     */
    public function getHeader(string $name): string
    {
        return self::getCompleteHeader()[$name] ?? '';
    }

    /**
     * 获取完整header
     *
     * @return array
     */
    public function getCompleteHeader(): array
    {
        return Container::getRequestHeader();
    }

    /**
     * 获取cookie
     *
     * @param string $name
     * @return string
     */
    public function getCookie(string $name): string
    {
        return self::getCompleteCookie()[$name] ?? '';
    }

    /**
     * 获取全部cookie
     *
     * @return array
     */
    public function getCompleteCookie(): array
    {
        return Container::getRequestCookie();
    }

    /**
     * 获取上传的全部文件
     *
     * @return array
     */
    public function getUploadFiles(): array
    {
        return Container::getRequestFiles();
    }

    /**
     * 获取请求的URI
     *
     * @return string
     */
    public function getUri(): string
    {
        return Container::getRequestUri();
    }

    /**
     * 获取请求方式
     *
     * @return string
     */
    public function getMethod(): string
    {
        return Container::getRequestMethod();
    }

    /**
     * 获取客户端IP
     *
     * @return string
     */
    public function getIP(): string
    {
        return Container::getRequestIP();
    }
}
