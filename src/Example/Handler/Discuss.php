<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2017/10/16
 * Time: 上午11:18
 */

namespace Handler;

use CoolQ\Core\CoolQ;

class Discuss
{
    private $cq;
    private $receive;
    private $log;

    public function __construct(CoolQ $cq)
    {
        $this->cq = $cq;
        $this->receive = $cq->receive;

    }

    public function onMessage()
    {

    }

    public function onRequest()
    {

    }

    public function onEvent()
    {

    }

}