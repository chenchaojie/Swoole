<?php
/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/7/4
 * Time: 19:56
 */

use Swoole\Entrance;

$rootPath = __DIR__;
require $rootPath . DIRECTORY_SEPARATOR . 'Swoole'. DIRECTORY_SEPARATOR . 'Entrance.php';

Entrance::run($rootPath);