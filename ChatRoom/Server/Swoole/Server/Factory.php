<?php
/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/7/5
 * Time: 13:39
 */

namespace Swoole\Server;
use Swoole\Core\Factory as CFactory;


class Factory
{
    public static function getInstance($adapter = 'Http')
    {
        $className = __NAMESPACE__ . "\\{$adapter}";
        return CFactory::getInstance($className);
    }
}