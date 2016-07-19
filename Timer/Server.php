<?php

/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/7/4
 * Time: 18:22
 */

/**
 * Class Server
 * 异步mysql连接池
 */
class Server
{
    private $_server = null;

    private $_pdo = null;

    public function __construct()
    {
        $this->_server = new swoole_server('127.0.0.1', 9501);

        $this->_server->set(array(
            'worker_num'      => 8,
            'dispatch_mode'   => 3,
            'task_worker_num' => 8
        ));


        $this->_server->on('start', array($this, 'onStart'));

        $this->_server->on('WorkerStart', array($this, 'onWorkerStart'));

        $this->_server->on('connect', array($this, 'onConnect'));

        $this->_server->on('receive', array($this, 'onReceive'));

        $this->_server->on('close', array($this, 'onClose'));

        $this->_server->on('task', array($this, 'onTask'));

        $this->_server->on('finish', array($this, 'onFinish'));

        $this->_server->start();
    }

    public function onStart( $serv ) {
        echo "Start\n";
    }

    public function onWorkerStart( $serv, $worker_id)
    {
        if ($worker_id >= $this->_server->setting['worker_num']) {
            $this->_pdo = new PDO(
                "mysql:host=127.0.0.1;port=3306;dbname=test",
                "root",
                "123456",
                array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8';",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_PERSISTENT => true
                )
            );
        }
    }

    public function onTask($serv, $task_id, $from_id, $data)
    {
        $sql = json_decode( $data , true );

        $statement = $this->_pdo->prepare($sql['sql']);
        $statement->execute($sql['param']);

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $serv->send( $sql['fd'],json_encode($result));
        return true;
    }

    public function onFinish()
    {

    }

//    public function onTimer($serv, $interval)
//    {
//        switch ($interval) {
//            case 500:
//                echo  'Do thing A at interval 500';
//                break;
//        }
//    }

    public function onConnect( $serv, $fd, $from_id ) {
        echo "Client {$fd} connect\n";
    }
    public function onReceive( swoole_server $serv, $fd, $from_id, $data ) {

        echo $data;
        $sql = array(
            'sql'   => 'select * from person where `number` = ?',
            'param' => array($data),
            'fd'    => $fd
        );

        $this->_server->task(json_encode($sql));

        $this->_server->send($fd, 'heheh...');
    }
    public function onClose( $serv, $fd, $from_id ) {
        echo "Client {$fd} close connection\n";
    }
}

$server = new Server();