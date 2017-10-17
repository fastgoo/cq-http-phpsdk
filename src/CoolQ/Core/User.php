<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2017/10/14
 * Time: 下午5:28
 */

namespace CoolQ\Core;

class User
{
    private $_core;

    public function __construct(Core $core)
    {
        $this->_core = $core;
    }

    /**
     * 获取QQ个人信息
     */
    public function getLoginQQInfo()
    {
        return $this->_core->callFunc(__FUNCTION__);
    }

    /**
     * 获取好友列表
     */
    public function getFriendList()
    {
        return $this->_core->callFunc(__FUNCTION__);
    }

    /**
     * 获取陌生人基础信息
     * @param string $qq
     * @param bool $cache
     * @return mixed
     */
    public function getStrangerInfo($qq = '', $cache = true)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('qq', 'cache'));
    }

    /**
     * 批量获取用户基础信息
     * @param array $qq
     * @return mixed
     */
    public function getMoreQQInfo(Array $qq)
    {
        $qqList = implode('-', $qq);
        return $this->_core->callFunc(__FUNCTION__, compact('qqList'));
    }

    /**
     * 好友请求操作
     * @param $responseFlag  事件标识
     * @param int $subType 1通过  2拒绝
     * @param string $name 通过以后的备注信息
     * @return mixed
     */
    public function setFriendAddRequest($responseFlag, $subType = 1, $name = '')
    {
        return $this->_core->callFunc(__FUNCTION__, compact('responseFlag', 'subType','name'));
    }

    /**
     * 发送消息给好友
     * @param $qq
     * @param string $msg
     * @return mixed
     */
    public function sendMsg($qq, $msg = '')
    {
        return $this->_core->callFunc('sendPrivateMsg', compact('qq', 'msg'));
    }

    /**
     * QQ打卡
     * @return mixed
     */
    public function setSign()
    {
        return $this->_core->callFunc(__FUNCTION__);
    }

    /**
     * 获取权限信息
     * @return mixed
     */
    public function getAuthInfo()
    {
        return $this->_core->callFunc(__FUNCTION__);
    }

    /**
     * 赞好友名片
     * @param $qq
     * @param int $number
     * @return mixed
     */
    public function sendLike($qq, $number = 1)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('qq', 'number'));
    }

}