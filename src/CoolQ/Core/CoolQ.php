<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2017/10/14
 * Time: 下午5:33
 */

namespace CoolQ\Core;

use CoolQ\Lib\Redis;

class CoolQ
{
    private $system;
    private $user;
    private $group;
    private $discuss;
    private $receive;
    private $core;
    private $redis;

    public function __construct($config)
    {
        $this->redis = New Redis(!empty($config['redis']) ? $config['redis'] : []);
        $this->core = New Core($config);
        $this->core->setRedis($this->redis);
        $this->group = new Group($this->core);
        $this->user = new User($this->core);
        $this->system = new System($this->core);
        $this->discuss = new Discuss($this->core);
        $this->receive = new Receive($config);
        $this->receive->setRedis($this->redis);

    }

    public function __get($name)
    {
        return $this->$name;
    }
}