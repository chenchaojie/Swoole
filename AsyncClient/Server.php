<?php
/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/7/1
 * Time: 9:21
 */

class Server
{
    private $_server = null;

    public function __construct()
    {
        $this->_server = new swoole_server('127.0.0.1', 9501);

        $this->_server->on('start', array($this, 'onStart'));

        $this->_server->on('connect', array($this, 'onConnect'));

        $this->_server->on('receive', array($this, 'onReceive'));

        $this->_server->on('close', array($this, 'onClose'));

        $this->_server->start();

    }

    public function onStart( $serv ) {
        echo "Start\n";
    }
    public function onConnect( $serv, $fd, $from_id ) {
        echo "Client {$fd} connect\n";
    }
    public function onReceive( swoole_server $serv, $fd, $from_id, $data ) {

        sleep(10);
        echo "Get Message From Client {$fd}:{$data}\n";
        // send a task to task worker.
        $this->_server->send($fd, 'heheh...');
    }
    public function onClose( $serv, $fd, $from_id ) {
        echo "Client {$fd} close connection\n";
    }

}

$server =  new Server();