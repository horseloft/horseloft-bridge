<?php
namespace Horseloft\Phalanx\Builder;

use Horseloft\Phalanx\HorseloftPhalanxException;
use Horseloft\Phalanx\Handler\Container;

class FileReader
{
    /**
     * @var string
     */
    private $applicationRoot;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @param string $applicationRoot
     * @param string $namespace
     */
    public function __construct(string $applicationRoot, string $namespace)
    {
        $this->applicationRoot = rtrim($applicationRoot, '/') . '/';

        $this->namespace = $namespace . '\\';

        Container::setNamespace($this->namespace);

        Container::setApplicationPath($this->applicationRoot);
    }

    /**
     * --------------------------------------------------------------------------
     *  读取路由配置
     * --------------------------------------------------------------------------
     */
    public function readAndSetRoute()
    {
        $routePath = $this->applicationRoot . 'Route';
        if (!is_dir($routePath)) {
            return;
        }
        $fileInfo = [];
        $handle = opendir($routePath);
        while (false !== $file = readdir($handle)) {
            if ($file == '.' || $file == '..' || !is_file($routePath . '/' . $file)) {
                continue;
            } else {
                try {
                    $suffix = substr($file, -4);
                    if ($suffix == false || $suffix != '.php') {
                        continue;
                    }
                    require_once $routePath . '/' . $file;
                } catch (\Exception $e){
                    continue;
                }
            }
        }
        closedir($handle);
    }

    /**
     * --------------------------------------------------------------------------
     * 设置服务的配置信息
     * --------------------------------------------------------------------------
     */
    public function readAndSetEnv()
    {
        $env = $this->readIniFile($this->applicationRoot . 'env.ini');
        if (empty($env)) {
            throw new HorseloftPhalanxException('missing env file');
        }

        // debug
        if ($env['debug'] === true) {
            Container::setDebug(true);
            error_reporting(-1);
        }

        // 错误信息是否写入日志【默认值true】
        if ($env['error_log'] === false) {
            Container::setErrorLog(false);
        }

        // 错误信息的追踪信息是否写入日志【默认值true】
        if ($env['error_log_trace'] === false) {
            Container::setErrorLogTrace(false);
        }

        // 日志目录、日志文件
        if (is_dir($env['log_path'])) {
            Container::setLogPath('/' . trim($env['log_path'], '/') . '/');
        } else {
            if (!is_dir($this->applicationRoot . 'Log')) {
                throw new HorseloftPhalanxException('missing log path');
            }
            Container::setLogPath($this->applicationRoot . 'Log/');
        }

        // 配置文件目录
        Container::setConfigPath($this->applicationRoot . 'Config/');

        // env.ini文件内容以数组格式保留
        Container::setEnv($env);
        unset($env);
    }

    /**
     * --------------------------------------------------------------------------
     *  设置全局配置信息
     * --------------------------------------------------------------------------
     */
    public function readSetConfig()
    {
        $configPath = Container::getConfigPath();
        if (!is_dir($configPath)) {
            throw new HorseloftPhalanxException('missing config path');
        }

        $handle = opendir($configPath);
        while (false !== $file = readdir($handle)) {
            if ($file == '.' || $file == '..') {
                continue;
            } else {
                try {
                    $suffix = substr($file, -4);
                    if ($suffix == false || $suffix != '.php') {
                        continue;
                    }
                    $configure = require_once $configPath . $file;
                    if (!is_array($configure)) {
                        continue;
                    }
                    Container::setConfig(substr($file, 0, -4), $configure);

                } catch (\Exception $e){
                    continue;
                }
            }
        }
        closedir($handle);
    }

    /**
     * --------------------------------------------------------------------------
     *  自动读取 Interceptor 目录下的类文件 并作为拦截器使用
     * --------------------------------------------------------------------------
     *
     * 1. 以小驼峰格式的文件名称作为拦截器名称
     *
     * 2. 类中必须有handle方法
     *
     * 3. handle方法必须有一个Request类型的参数
     *
     * 4. Request类全路径：Horseloft\Core\Drawer\Request
     */
    public function readSetInterceptor()
    {
        $dir = $this->applicationRoot . 'Application/Interceptor';
        if (!is_dir($dir)) {
            return;
        }

        $interceptor = [];
        $namespace = $this->namespace . 'Interceptor\\';
        $handle = opendir($dir);
        while (false !== $file = readdir($handle)) {
            if ($file == '.' || $file == '..') {
                continue;
            } else {
                try {
                    $suffix = substr($file, -4);
                    if ($suffix == false || $suffix != '.php') {
                        continue;
                    }
                    $interceptorName = ucfirst(substr($file, 0, -4));
                    $interceptorClass = $namespace . $interceptorName;
                    $cls = new \ReflectionClass($interceptorClass);
                    $method = $cls->getMethod('handle');
                    $methodNumber = $method->getNumberOfParameters();
                    if ($methodNumber == 0) {
                        throw new HorseloftPhalanxException(
                            'Interceptor[' . $interceptorName . '->handle] missing parameter: Request'
                        );
                    }
                    if ($methodNumber > 1) {
                        throw new HorseloftPhalanxException(
                            'Interceptor[' . $interceptorName . '->handle] allow only a [Request] type parameter'
                        );
                    }

                    $params = $method->getParameters();
                    $paramClass = $params[0]->getClass();
                    if (is_null($paramClass)) {
                        throw new HorseloftPhalanxException(
                            'Interceptor[' . $interceptorName . '->handle] first parameter must [Request]'
                        );
                    }

                    $paramClassName = $paramClass->getName();
                    if ($paramClassName != 'Horseloft\Phalanx\Builder\Request') {
                        throw new HorseloftPhalanxException(
                            'Interceptor[' . $interceptorName . '->handle] first parameter must [Request]'
                        );
                    }
                    $interceptor[$interceptorName] = [$interceptorClass, 'handle'];

                } catch (\Exception $e){
                    throw new HorseloftPhalanxException($e->getMessage() . PHP_EOL . $e->getTraceAsString());
                }
            }
        }
        Container::setInterceptor($interceptor);
        closedir($handle);
        unset($interceptor);
    }

    /**
     * 获取ini文件值
     *
     * @param string $filename
     * @return array
     */
    private function readIniFile(string $filename)
    {
        $iniData = parse_ini_file($filename, true, INI_SCANNER_RAW);
        if ($iniData === false) {
            return [];
        }
        foreach ($iniData as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $iniData[$key][$k] = $this->iniValueConvert($v);
                }
            } else {
                $iniData[$key] = $this->iniValueConvert($value);
            }
        }
        return $iniData;
    }

    /**
     * ini文件值转换为php变量类型
     *
     * @param string $value
     * @return bool|float|int|string|null
     */
    private function iniValueConvert(string $value)
    {
        // true
        if (strtolower($value) === 'true') {
            return true;
        }

        // false
        if (strtolower($value) === 'false') {
            return false;
        }

        // null
        if (strtolower($value) === 'null') {
            return null;
        }

        // 数字
        if (is_numeric($value)) {
            if (strpos($value, '.') === false) {
                return intval($value);
            } else {
                return doubleval($value);
            }
        } else {
            return $value;
        }
    }
}
