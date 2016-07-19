<?php

/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/7/13
 * Time: 16:21
 */
class Crontab
{
    private $_serv = null;

    private $config;

    private $event_list;

    const MINUTE = 60 * 1000;

    const HOUR = 60 * Crontab::MINUTE;

    const DAY = 24 * Crontab::HOUR;
}


$mintue = '*/4';

$hour = '*/6';

$res = preg_split('/\//', $time);

var_dump(strlen($res));