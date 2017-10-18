<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2017/10/18
 * Time: 下午3:10
 */
require __DIR__ . '/vendor/autoload.php';
require 'config.php';

use CoolQ\Core\CoolQ;

$cq = new CoolQ($config);

if(!empty($_GET['key']) && $_GET['key'] == 773729704){
    $cq->redis->deleteLog();
}

echo $cq->redis->getLog();