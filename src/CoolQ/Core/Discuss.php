<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2017/10/14
 * Time: 下午5:27
 */

namespace CoolQ\Core;

class Discuss
{
    private $_core;

    public function __construct(Core $core)
    {
        $this->_core = $core;
    }

    /**
     * 离开讨论组
     * @param $group
     * @return mixed
     */
    public function setDiscussLeave($group)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('group'));
    }

    /**
     * 发送讨论组数据
     * @param $group 讨论组号
     * @param string $msg 消息
     * @return mixed
     */
    public function sendMsg($group, $msg = '')
    {
        return $this->_core->callFunc('sendDiscussMsg', compact('group', 'msg'));
    }
}