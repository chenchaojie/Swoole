<?php
/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/6/30
 * Time: 20:02
 */
$serv = new swoole_server('127.0.0.1', 9502);

$serv->tick(2000, function ($timer_id) {
    echo "tick-2000ms\n";
});
//$serv->set(array('task_worker_num' => 4));
//
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
//    $task_id = $serv->task($data);
//    echo "Dispath AsyncTask: id=$task_id\n";

    $serv->send($fd, $data);
});
//
////一个task worker 执行成功的回调
//$serv->on('task', function ($serv, $task_id, $from_id, $data) {
//    // handle the task, do what you want with $data
//    echo "New AsyncTask[id=$task_id]".PHP_EOL;
//
//    // after the task task is handled, we return the results to caller worker.
//    $serv->finish("$data -> OK");
//});
//
//$serv->on('finish', function ($serv, $task_id, $data) {
//    echo "AsyncTask[$task_id] Finish: $data".PHP_EOL;
//});

$serv->start();