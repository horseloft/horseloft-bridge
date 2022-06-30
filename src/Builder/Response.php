<?php

namespace Horseloft\Phalanx\Builder;

use Horseloft\Phalanx\Handler\Container;

class Response
{
    /**
     * 数据数据
     *
     * @param $data
     */
    public static function echo($data)
    {
        echo self::return($data);
    }

    /**
     * @param $data
     *
     * @return false|mixed|string
     */
    public static function return($data)
    {
        $envData = Container::getEnv();
        if (!empty($envData['response_content_type'])) {
            header('Content-Type:' . $envData['response_content_type']);
        } else {
            header('Content-Type:application/json; charset=UTF-8');
        }
        if (is_array($data) || is_object($data)) {
            return json_encode($data);
        } else {
            return $data;
        }
    }

    /**
     * 输出数据并终止程序
     *
     * @param $data
     */
    public static function exit($data = null)
    {
        echo self::return($data);
        exit();
    }

    /**
     * 格式化打印数据并终止程序
     *
     * @param $data
     */
    public static function format($data)
    {
        echo "<pre>";
        print_r($data);
        echo "<pre/>";
        exit();
    }

    /**
     * 打印数据并终止程序
     *
     * @param $data
     */
    public static function print($data)
    {
        print_r($data);
        exit();
    }
}
