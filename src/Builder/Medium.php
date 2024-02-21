<?php

namespace Isobaric\Phalanx\Builder;

use Isobaric\Phalanx\ShutdownException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * 共用方法集合
 */
class Medium
{
    /**
     * 反射执行方法
     *
     * @param string $class
     * @param string $function
     * @param mixed ...$args
     * @return mixed
     */
    public static function reflectionMultiple(string $class, string $function, ...$args)
    {
        try {
            $newInstance = (new ReflectionClass($class))->newInstance();
            $refMethod = new ReflectionMethod($newInstance, $function);
            return $refMethod->invoke($newInstance, ...$args);
        } catch (ReflectionException $e) {
            throw new ShutdownException($function . ' invoke Failed');
        }
    }
}
