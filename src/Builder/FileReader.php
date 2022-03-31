<?php
namespace Horseloft\Bridge\Builder;

use Horseloft\Bridge\BridgeException;
use Horseloft\Bridge\Handler\Container;

class FileReader
{
    /**
     * @var string
     */
    private $applicationRoot;

    /**
     * @param string $applicationRoot
     */
    public function __construct(string $applicationRoot)
    {
        $this->applicationRoot = trim($applicationRoot, '/') . '/';
    }

    /**
     * --------------------------------------------------------------------------
     * 设置服务的配置信息
     * --------------------------------------------------------------------------
     */
    public function readAndSetEnv()
    {
        $env = $this->readIniFile($this->applicationRoot . 'env.ini');
        if (empty($application)) {
            throw new BridgeException('missing env file');
        }

        // debug
        if ($env['debug'] === true) {
            Container::setDebug(true);
        }

        // 日志目录、日志文件
        if (is_dir($env['log_path'])) {
            Container::setLogPath(trim($env['log_path'], '/') . '/');
        } else {
            if (!is_dir($this->applicationRoot . 'Log')) {
                throw new BridgeException('missing log path');
            }
            Container::setLogPath($this->applicationRoot . 'Log/');
        }

        // 配置文件目录
        Container::setConfigPath($this->applicationRoot . 'Config/');

        // env.ini文件内容以数组格式保留
        Container::setEnv($env);
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
            throw new BridgeException('missing config path');
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
