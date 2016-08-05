<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/5
 * Time: 13:50
 */
require_once "DbServer.php";
$config = require_once "config.php";


$server = new DbServer($config);

$server->run();