<?php

/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/7/6
 * Time: 16:07
 */
class HttpServer
{
    private $_server = null;

    public function __construct()
    {
        $this->_server = new swoole_http_server('0.0.0.0', 80);

        $this->_server->set(
            array(
                'worker_num'    => 4,
                'daemonize'     => false,
                'max_request'   => 1000,
                'dispatch_mode' => 1
            )
        );

        $this->_server->on('Start', array($this, 'onStart'));
        $this->_server->on('request', array($this, 'onRequest'));
//        $this->_server->on('message', array($this, 'onMessage'));

        $this->_server->start();
    }

    //启动服务器
    public function onStart($serv)
    {
        echo "start \n";
    }

    public function onRequest($request,  $response)
    {
        print_r($request->get);

        $response->end("<h1>hello world!</h1>");
    }

//    public function onMessage($request, $response)
//    {
//        echo $request->message;
//        $response->message(json_encode(array('data1', 'data2')));
//    }
}

new HttpServer();