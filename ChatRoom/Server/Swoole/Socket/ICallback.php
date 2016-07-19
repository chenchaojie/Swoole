<?php
/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/7/5
 * Time: 14:08
 */

namespace Swoole\Socket;


interface ICallback
{
    /**
     * 当socket服务启动时 回调方法
     *
     * @return mixed
     */
    public function onStart();

    public function onConnect();

    public function onReceive();

    public function onClose();

}