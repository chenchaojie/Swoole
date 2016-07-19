<?php
/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/6/30
 * Time: 19:35
 */

$ws = new swoole_websocket_server('0.0.0.0', 9502);

$ws->on('open', function ($ws, $request) {
    var_dump($request->fd, $request->get, $request->server);
    $ws->push($request->fd, "hello world\n");
});

$ws->on('message', function ($ws, $frame) {
    echo "Message: {$frame->data}\n";
    $ws->push($frame->fd, "server: {$frame->data}");
});

$ws->on('close', function ($ws, $fd) {
    echo "client-{$fd} is closed\n";
});

$ws->start();