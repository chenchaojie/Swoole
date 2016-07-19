<?php

/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/7/13
 * Time: 13:58
 */
class Server
{
    private $_serv = null;

    public function __construct()
    {
        $this->_serv = new swoole_websocket_server('0.0.0.0', 80);

        $this->_serv->on('open', array($this, 'onStart'));
        $this->_serv->on('message', array($this, 'onMessage'));
        $this->_serv->on('close', array($this, 'onClose'));

        $this->_serv->start();
    }

    public function onStart($serv, $request)
    {
        echo "server: connect success with fd {$request->fd}\n";
    }

    public function onMessage($serv, $frame)
    {
        echo "receive from {$frame->fd} data:{$frame->data} fin:{$frame->finish}\n";
        $serv->push($frame->fd, "this is server");
    }

    public function onClose($serv, $fd)
    {
        echo "client {$fd} closed\n";
    }
}

new Server();