<?php

/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/7/6
 * Time: 11:14
 */
class Server
{
    private $_server = null;

    private $_n ;

    public function __construct()
    {
        $this->_server = new swoole_server('127.0.0.1', 9501);

        $this->_server->set(array(
            'worker_num'         => 4,
            'daemonize'          => false,
            'max_request'        => 2000,
            'dispatch_mode'      => 2,
            'package_max_length' => 8192,
            'open_eof_check'     => true,
            'package_eof'        => "\r\n"
        ));

        $this->_server->on('Start', array($this, 'onStart'));
        $this->_server->on('workerstart', array($this, 'onWorkerStart'));
        $this->_server->on('Connect', array($this, 'onConnect'));
        $this->_server->on('Receive', array($this, 'onReceive'));
        $this->_server->on('Close', array($this, 'onClose'));

        $this->_server->start();
    }

    public function onStart($serv)
    {
        echo  "master Start\n";
    }

    public function onWorkerStart($serv, $worker_id)
    {
        $this->_n = 100;

    }

    public function onConnect($serv, $fd, $from_id)
    {
        echo "client {$fd} connect\n";
    }

    public function onReceive($serv, $fd, $from_id, $data)
    {
        echo "recv {$data}";
        swoole_async_write('test.log', $data, -1, function($filename, $writen){
            sleep(5);
            echo "filename:{$filename}, {$writen} btye";
        });

        echo "continue...deal";
//        echo $this->_n."\n";
//        $this->_n = $data;
//        echo $this->_n."\n";

        // EOF 自定义包
        /*$data_list = explode("\r\n", $data);
        foreach ($data as $msg) {
            // 考虑空包的情况
            if (!empty($msg)) {
                echo "GET Message from client {$fd}: {$msg}\n";
            }
        }*/

//        // 固定长度自定义包
//        $length = unpack("N" , $data)[1];
//        echo "Length = {$length}\n";
//        $msg = substr($data,-$length);
//        echo "Get Message From Client {$fd}:{$msg}\n";
    }

    public function onClose($serv ,$fd, $from_id)
    {
        echo "client {$fd} close connection\n";
    }

}


new Server();

