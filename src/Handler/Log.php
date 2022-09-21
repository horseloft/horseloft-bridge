<?php

namespace Horseloft\Phalanx\Handler;

final class Log
{
    public static function log(string $message, string $filename = '', string $level = 'info')
    {
        self::write($message, $level, $filename);
    }

    /**
     * 日志记录
     *
     * @param string $message
     */
    public static function info(string $message)
    {
        self::write($message, 'info', Container::getLogFilename());
    }

    /**
     * 日志记录
     *
     * @param string $message
     */
    public static function error(string $message)
    {
        self::write($message, 'error', Container::getLogFilename());
    }

    /**
     * 日志记录
     *
     * @param string $message
     */
    public static function debug(string $message)
    {
        self::write($message, 'debug', Container::getLogFilename());
    }

    /**
     * warning日志
     *
     * @param string $message
     */
    public static function warning(string $message)
    {
        self::write($message, 'warning', Container::getLogFilename());
    }

    /**
     * @param string $message
     * @param string $level
     * @param string $filename
     */
    private static function write(string $message, string $level, string $filename)
    {
        if ($filename == '') {
            $filename = Container::getLogFilename();
        }
        $file = Container::getLogPath() . $filename . '.log';

        $inc = 0;
        while (true)
        {
            $inc++;
            // 100M
            if (!is_file($file) || filesize($file) < 104857600) {
                break;
            }
            $file = Container::getLogPath() . $filename . '-' . $inc . '.log';
        }
        file_put_contents($file, date('Y-m-d H:i:s ') . $level . ': ' . $message . "\n", FILE_APPEND);
    }
}
