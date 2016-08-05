<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/5
 * Time: 17:08
 */
class Client
{
    private $client ;

    public function __construct()
    {
        $this->client = new swoole_client(SWOOLE_SOCK_TCP);
    }

    public function connect()
    {
        $this->client->connect('127.0.0.1', 9905, 10);
    }

    public function send($msg)
    {
        $this->client->send($msg);

        return $this->client->recv();
    }

    public function close()
    {
        $this->client->close();
    }
}

    $c = new Client();
    $c->connect();
    $r = $c->send('hello dbpool');
    var_dump($r);
    $c->close();
