<?php
/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/6/30
 * Time: 9:11
 */


$serv = new swoole_server("127.0.0.1", 9502);

$serv->set(array(
    'worker_num' => 4,    //worker process num
    'max_request' => 50,
    'dispatch_mode' => 3,
    ''
));

//  注册连接的事件句柄
$serv->on('connect', function ($serv, $fd) {
    echo "Client:connect.\n";
});

// 注册一个接收请求数据的事件句柄
$serv->on('receive', function ($serv, $fd, $from_id, $data) {

    echo 'length=:'.strlen($data)."work_id={$serv->worker_pid}\n";
//    $serv->send($fd, $data);
});


$serv->on('close', function ($serv, $fd) {
    echo  "Client: close.\n";
});
// 启动9501端口的服务
$serv->start();