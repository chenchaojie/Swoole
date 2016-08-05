<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/5
 * Time: 10:40
 */
class EventClient
{
    public $client;

    public function __construct()
    {
        $this->client = new swoole_client(SWOOLE_SOCK_TCP);

        if (!$this->client->connect('127.0.0.1', 9550, 4)) {
            exit('event连接失败');
        }
    }

    public function send($data)
    {
        $this->client->send($data);

        $recv1 = $this->client->recv();
        echo $recv1;
        $recv2 = $this->client->recv();
        echo $recv2;
        return $recv1.'+++'. $recv2;
    }
}

$client = new EventClient();

$r  = $client->send('1111');

var_dump($r);