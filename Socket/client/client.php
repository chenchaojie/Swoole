
<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/8
 * Time: 11:15
 */

error_reporting(E_ALL);

set_time_limit(0);


class client
{
    private $sock;

    public function __construct()
    {
        $this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        socket_connect($this->sock, '127.0.0.1', 9000);
    }

    public function send($data)
    {
        socket_write($this->sock, $data, strlen($data));
    }

    public function __destruct()
    {
        socket_close($this->sock);
    }

    public function read()
    {
        return socket_read($this->sock, 100);
    }
}

$c  =new client();

$c->send(str_repeat('hehe', 250));
echo $c->read();
