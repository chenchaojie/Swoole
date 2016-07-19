<?php
/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/7/5
 * Time: 10:33
 */

namespace Swoole\Core;

use Swoole\Common\Dir;

class Config
{
    private static $config;

    public static function load($configPath)
    {
        $files = Dir::tree($configPath, '/.php$/');

        $config = array();

        if (!empty($files)) {
            foreach ($files as $file) {
                $config += include "{$file}";
            }
        }

        self::$config = $config;
        return $config;
    }

    public static function loadFields(array $files)
    {
        $config = array();
        foreach ($files as $file) {
            $config += include "{$file}";
        }

        self::$config = $config;
        return $config;
    }

    public static function get($key, $default = null, $throw = false)
    {
        $result = isset(self::$config[$key]) ? self::$config[$key] : $default ;
        if ($throw && empty($result)) {
            throw new \Exception("{key} config empty");
        }

        return $result;
    }

    public static function all()
    {
        return self::$config;
    }
}