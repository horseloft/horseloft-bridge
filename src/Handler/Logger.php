<?php

namespace Horseloft\Bridge\Handler;

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
        $header = 'date=' . date('Y-m-d H:i:s') . '; ';
        file_put_contents(Container::getLogPath() . $filename, $header . $message . "\n", FILE_APPEND);
    }
}
