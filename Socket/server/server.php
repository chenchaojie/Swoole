<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/8
 * Time: 11:15
 */
error_reporting(E_ALL);

set_time_limit(0);


class server
{


    private $sock ;

    public function __construct()
    {
        $sock= socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        socket_bind($sock, '127.0.0.1', 9905);

        socket_listen($sock, 5);

        $this->sock = socket_accept($sock);

        $r = socket_read($this->sock, 2048);

        echo $r.'+++'.strlen($r)."\n";


        socket_close($this->sock);

        socket_close($sock);
    }
}

new server();