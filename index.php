<?php
/**
 * Created by PhpStorm.
 * User: chenchaojie
 * Date: 2016/6/30
 * Time: 9:16
 */

function gen(){

    $res= (yield say());

    $res2 = (yield $res."hello wordl");
}

function f()
{
    $res = (yield "eee");

    $res1= (yield $res."cxxx");
}

function say()
{
    sleep(5);
    return  "hhhhh\n";
}


$g = gen();
$f = f();
$s1= $g->current();
var_dump($s1);
$s2=$f->current();
var_dump($s2);
$g->send($s1);
var_dump($g->current());

$f->send($s2);
var_dump($f->current());

//$g->next();
//var_dump($g->current());