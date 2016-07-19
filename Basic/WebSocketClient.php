<?php
/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/6/30
 * Time: 19:46
 */

$cli = new swoole_http_client('127.0.0.1', 9502);

$cli->on('message', function ($_cli, $frame) {
    var_dump($frame);
});

$cli->upgrade('/', function ($cli) {
    echo $cli->body;
    $cli->push("hello world");
});