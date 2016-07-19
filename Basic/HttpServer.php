<?php
/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/6/30
 * Time: 19:24
 */

$http = new swoole_http_server('0.0.0.0', 80);

$http->on('request', function($request, $response) {
    $response->header("Content-Type", "text/html; charset=utf-8");
    $response->end("<h1>hello swoole</h1>");
});

$http->start();