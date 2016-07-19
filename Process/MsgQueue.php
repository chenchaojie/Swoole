<?php
/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/7/7
 * Time: 20:02
 */

class BaseProcess
{

    private $process;

    public function __construct()
    {
        $this->process = new swoole_process(array($this, 'run'), false, true);

        if (! $this->process->useQueue(123)) {
            var_dump(swoole_strerror(swoole_errno()));
            exit;
        }

        $this->process->start();
        while(true) {
           $data = $this->process->pop();
            echo "RECV: ". $data . PHP_EOL;
        }
    }

    public function run($worker)
    {
        swoole_timer_tick(1000, function($timer_id){
            static $index = 0;
            $index = $index + 1;
        });
    }
}