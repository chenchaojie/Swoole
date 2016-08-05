<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/5
 * Time: 13:55
 */
class DbServer
{
    private $__serv ;

    private $_db_config;

    private $_wait_queue = []; //等待数组

    private $_free_task_table;

    private $_busy_task_table;

    private $_request_size = 0;

    public function __construct($config)
    {
        $this->_db_config = $config;

        $this->_serv = new swoole_server('0.0.0.0', 9905);

        $this->_serv->set(array(
            'worker_num'       => 1,
            'task_worker_num'  => 2,
            'task_max_request' => 0,
            'max_request'      => 0,
            'log_file'         => '/tmp/swoole_dbpool.log',
            'dispatch_mode'    => 2
        ));


        $this->_serv->on('Start', array($this, 'onStart'));
        $this->_serv->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->_serv->on('Receive', array($this, 'onReceive'));
        // Task 回调的2个必须函数
        $this->_serv->on('Task', array($this, 'onTask'));
        $this->_serv->on('Finish', array($this, 'onFinish'));
    }

    public function run()
    {
        $this->_wait_queue = [];

        $this->_free_task_table = new swoole_table(1024);
        $this->_free_task_table->column('free_id', swoole_table::TYPE_STRING, 1000);
        $this->_free_task_table->create();
        $this->_free_task_table->set('list', array('free_id' => json_encode(range(0, 1))));


        $this->_busy_task_table = new swoole_table(1024);
        $this->_busy_task_table->column('busy_id', swoole_table::TYPE_STRING, 1000);
        $this->_busy_task_table->create();
        $this->_busy_task_table->set('list', array('busy_id' => json_encode(array())));

        $this->_request_size = 0;

        $this->_serv->start();
    }

    public function onStart($serv)
    {
        swoole_set_process_name("php5 master {$serv->master_pid}");
    }

    public function onWorkerStart($serv, $worker_id)
    {
        if ($worker_id >= $serv->setting['worker_num']) {
            swoole_set_process_name("php5 task {$worker_id}");
        } else {
            swoole_set_process_name("php5 worker {$worker_id}");

            $server = $this;
            swoole_timer_tick(500, function() use ($server){
                if (!empty($server->_wait_queue)) {

                    $data = array_shift($server->_wait_queue);

                    $task_id = $server->_getFreeTaskId($data['fd']);

                    if ($task_id == -1) {
                        $server->_wait_queue[] = $data;
                    } else {
                        $server->_serv->task(json_encode($data), $task_id);
                    }
                }
//                echo "nothing doing...";
            });

        }
    }

    public function onReceive($serv, $fd, $from_id, $data)
    {
        $data = array('fd' => $fd, 'send_data' => $data);

        $task_id = $this->_getFreeTaskId($fd);

        var_dump($this->_wait_queue);

        if ($task_id == -1) {
            $this->_wait_queue[] = $data;
            echo '111';
        } else {
            $serv->task(json_encode($data), $task_id);
        }

        $this->_request_size++;
    }

    public function onTask($serv, $task_id, $from_id, $data)
    {
        $data = json_decode($data, true);

        $fd = $data['fd'];
        $send_data = $data['send_data'];

        unset($data);

        $result = $this->deal($fd, $send_data);

        $serv->send($fd, $result);
    }

    public function onFinish($serv,$task_id, $data)
    {
        
    }

    private function _getFreeTaskId($fd)
    {
        $task_id = $this->popFreeList();

        if ($task_id !== false) {

            $this->addBusyList($fd, $task_id);

            return $task_id;

        } else {
            return -1;
        }
    }

    public function deal($fd, $send_data)
    {
        sleep(5);
        $result = $send_data.'-->finish';

        $task_id = $this->popBusyList($fd);

        $this->addFreeList($task_id);

        return $result;
    }

    private function addBusyList($fd, $task_id)
    {
        $busy_ids = $this->_busy_task_table->get('list')['busy_id'];
        $busy_ids = json_decode($busy_ids, true);

        $busy_ids[$fd] = $task_id;

        $this->_busy_task_table->set('list', array('busy_id' => json_encode($busy_ids)));
    }

    private function addFreeList($task_id)
    {
        $free_ids = $this->_free_task_table->get('list')['free_id'];
        $free_ids = json_decode($free_ids, true);

        $free_ids[] = $task_id;

        $this->_free_task_table->set('list', array('free_id' => json_encode(array_values($free_ids))));
    }

    private function popBusyList($fd)
    {
        $busy_ids = $this->_busy_task_table->get('list')['busy_id'];
        $busy_ids = json_decode($busy_ids, true);

        $task_id = $busy_ids[$fd];
        unset($busy_ids[$fd]);

        $this->_busy_task_table->set('list', array('busy_id' => json_encode($busy_ids)));

        return $task_id;
    }

    private function popFreeList()
    {
        $free_ids = $this->_free_task_table->get('list')['free_id'];
        $free_ids = json_decode($free_ids, true);

        if (!empty($free_ids)) {

            $task_id = array_shift($free_ids);
            $this->_free_task_table->set('list', array('free_id' => json_encode($free_ids)));

            return $task_id;

        } else {
            return false;
        }

    }
}