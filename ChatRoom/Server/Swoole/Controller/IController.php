<?php
/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/7/5
 * Time: 14:37
 */

namespace Swoole\Controller;


interface IController
{
    /**
     * 设置服务
     */
    public function setServer($server);

    //切面的使用
    public function _before();

    public function _after();


}