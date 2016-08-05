<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/5
 * Time: 10:53
 */
class Server
{

    private $serv = null;

    public static $arr = [];

    public $config = array(
        'worker_num'               => 2,
        'task_worker_num'          => 1,
        'task_ipc_mode'            => 1,
        'heartbeat_check_interval' => 300,
        'heartbeat_idle_time'      => 300,
    );

    public function __construct()
    {
        $this->serv = new swoole_server('0.0.0.0', 9550);

        $this->serv->set($this->config);

        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->serv->on('Task', array($this, 'onTask'));
        $this->serv->on('Finish', array($this, 'onFinish'));
    }

    public function run()
    {
        $this->serv->start();
    }

    public function onStart($serv)
    {
        echo "php server 启动 master_id={$serv->master_pid};manager_id={$serv->manager_pid}\n";
    }

    public function onConnect($serv, $fd, $from_id)
    {
        echo "php connect from {$fd}\n";
    }

    public function onReceive($serv, $fd, $from_id, $data)
    {
        $task_data['msg'] = $data;
        $task_data['fd'] = $fd;
//        sleep(1);
//        $task_id = $serv->task($task_data, 0);
        $p = ($this->serv == $serv);

        var_dump($p);
        $serv->send($fd, $p);
//        echo "{$task_id}\n";

        return;
    }

    public function onWorkerStart($serv, $worker_id)
    {
        if ($worker_id >= $serv->setting['worker_num']) {
            swoole_set_process_name("php5 task worker");
        } else {
            swoole_set_process_name("php5 worker worker");
        }
    }

    public function onTask($serv, $task_id, $from_id, $data)
    {
        echo "task start {$task_id}\n";

        sleep(3);

        $data['msg'] .= "-->task deal";

        return $data;
    }

    public function onFinish($serv, $task_id, $data)
    {
        $serv->send($data['fd'], $data['msg']);
    }
}

(new Server())->run();