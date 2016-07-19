<?php
/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/7/5
 * Time: 14:08
 */

namespace Swoole\Socket;

use Swoole\Socket\IServer,
    Swoole\Socket\ICallback;

class Swoole
{
    private $client;
    private $config;
    private $serv;

    public function __construct(array $config)
    {
        if (!extension_loaded('swoole')) {
            throw new \Exception("no swoole extension. get: https://github.com/matyhtf/swoole");
        }

        $this->config = $config;
        $this->serv = new \swoole_server($config['host'], $config['port'], $config['work_mode']);
        $this->serv->set(array(
            'reactor_num'     => empty($config['reactor_num']) ? 2 : $config['reactor_num'],
            'worker_num'      => empty($config['worker_num']) ? 2 : $config['worker_num'],
            'task_worker_num' => empty($config['task_worker_num']) ? 2 : $config['task_worker_num'],
            'backlog'         => empty($config['backlog']) ? 128 : $config['backlog'],
            'max_request'     => empty($config['max_request']) ? 1000 : $config['max_request'],
            'max_conn'      => empty($config['max_conn']) ? 100000 : $config['max_conn'],
            'dispatch_mode' => empty($config['dispatch_mode']) ? 2 : $config['dispatch_mode'],
            'log_file'      => empty($config['log_file']) ? '/tmp/swoole.log' : $config['log_file'],
            'daemonize'     => empty($config['daemonize']) ? 0 : 1,
        ));
    }

    public function setClient($client)
    {
        $this->client = $client;
        return true;
    }

    public function run()
    {
        $this->serv->on('Start', array($this->client, 'onStart'));
        $this->serv->on('Connect', array($this->client, 'onConnect'));
        $this->serv->on('Receive', array($this->client, 'onReceive'));
        $this->serv->on('Close', array($this->client, 'onClose'));

        $handlerArray = array(
            'onTimer',
            'onWorkerStart',
            'onWorkerStop',
            'onTask',
            'onFinish'
        );

        foreach ($handlerArray as $handler) {
            if (method_exists($this->client, $handler)) {
                $this->serv->on(
                    str_replace('on', '', $handler),
                    array($this->client, $handler)
                );
            }
        }

        $this->serv->start();
    }

}
