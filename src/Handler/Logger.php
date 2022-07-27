<?php

namespace Horseloft\Phalanx\Handler;

class Logger
{
    /**
     * 日志记录
     *
     * @param string $message
     * @param string $filename
     */
    public static function info(string $message, string $filename = '')
    {
        self::write($message, '', $filename);
    }

    /**
     * 日志记录
     *
     * @param string $message
     * @param string $filename
     */
    public static function error(string $message, string $filename = '')
    {
        self::write($message, 'error', $filename);
    }

    /**
     * 日志记录
     *
     * @param string $message
     * @param string $filename
     */
    public static function debug(string $message, string $filename = '')
    {
        self::write($message, 'debug', $filename);
    }

    /**
     * @param string $message
     * @param string $level
     * @param string $filename
     */
    private static function write(string $message, string $level, string $filename)
    {
        if ($filename == '') {
            $filename = (($level == '') ? '' : $level . '-') . Container::getLogFilename();
        }
        file_put_contents(Container::getLogPath() . $filename, $message . "\n", FILE_APPEND);
    }
}
