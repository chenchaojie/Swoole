<?php
/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/6/30
 * Time: 9:11
 */

$cli = new Swoole_client(SWOOLE_SOCK_TCP);

$cli->connect('127.0.0.1', 9501, 1);

fwrite(STDOUT, '输入消息：');
$msg = trim(fgets(STDIN));

$cli->send($msg);

echo $cli->recv()."\n";
