<?php

/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/7/7
 * Time: 16:53
 */
class Server
{
    private $_serv = null;

    private $_workers = [];

    private $_worker_num = 2;

    public function __construct()
    {
        $this->_serv = new swoole_server('127.0.0.1', 9501);

        $this->_serv->on('start', array($this, 'onStart'));
        $this->_serv->on('workerstart', array($this, 'onWorkerStart'));
        $this->_serv->on('connect', array($this, 'onConnect'));
        $this->_serv->on('receive', array($this, 'onReceive'));
        $this->_serv->on('close', array($this, 'onClose'));

        $this->_serv->start();
    }

    public function onStart($serv)
    {
        echo "start \n";
    }

    public function onWorkerStart($serv, $worker_id)
    {
        echo "worker start\n";
    }
    
    public function onConnect($serv, $fd, $from_id )
    {
        echo "connect..\n";
    }

    public function onReceive(swoole_server $serv, $fd, $from_id, $data)
    {
//        $process = new swoole_process(array($this, 'onProcess'), true);
//        $process->name('process php');
//        $pid = $process->start();
//
//        swoole_event_add($process->pipe, function($pipe) use ($process){
//            echo "child process: {$process->read()}\n";
//
//        });
//        echo "continue..";
//
//        swoole_process::wait(true);

        for ($i = 0; $i < $this->_worker_num ;$i++) {
            $process = new swoole_process(array($this, 'onProcess'), false, false);
            $process->useQueue();
            $pid = $process->start();
            $this->_workers[$pid] = $process;
        }

        foreach ($this->_workers as $pid => $worker) {
            $process->push("hello worker[{$pid}]\n");
            $result = $process->pop();
            echo "From worker: $result\n";//这里主进程，接受到的子进程的数据
        }

        for($i = 0; $i < $this->_worker_num; $i++)
        {
            $ret = swoole_process::wait();
            $pid = $ret['pid'];
            unset($this->_workers[$pid]);
            echo "Worker Exit, PID=".$pid.PHP_EOL;
        }

    }

    public function onProcess($worker)
    {
        $recv = $worker->pop();


//        sleep(2);
        echo "FROM master {$recv}\n";
        $worker->push("heheh parent");


        $worker->exit(0);
//        sleep(5);
//        $worker->write('child process');


    }

    public function onClose($serv, $fd, $from_id)
    {
        echo "close.\n";
    }
}

new Server();
/**
 * swoole_process 的创建默认是创建管道 当用消息队列是 可以将 第三个参数改为false
 *
 *
 */