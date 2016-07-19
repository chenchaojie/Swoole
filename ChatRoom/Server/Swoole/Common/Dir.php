<?php
/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/7/5
 * Time: 10:41
 */

namespace Swoole\Common;


class Dir
{

    public static function make($dir, $mode = 0755)
    {
        if (is_dir($dir) || mkdir($dir, $mode, true)) {
            return true;
        }

        return false;
    }

    public static function tree($dir, $filter = '', &$result = array(), $deep = false)
    {
        $files = new \DirectoryIterator($dir);

        foreach ($files as $file) {
            $filename = $file->getFilename();
            if ($filename[0] === '.') {
                continue;
            }

            if ($file->isDir()) {
                self::tree($dir . DS . $filename, $filter, $result, $deep);
            } else {
                if (!empty($filter) && !preg_match($filter, $filename)) {
                    continue;
                }

                if ($deep) {
                    $result[$dir] = $filename;
                } else {
                    $result[] = $dir . DS .$filename;
                }
            }
        }

        return $result;
    }

}