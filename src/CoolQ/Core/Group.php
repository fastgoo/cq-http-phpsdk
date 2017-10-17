<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2017/10/14
 * Time: 下午5:27
 */

namespace CoolQ\Core;

class Group
{

    private $_core;

    public function __construct(Core $core)
    {
        $this->_core = $core;
    }

    /**
     * 给指定群用户送花
     * @param $group
     * @param $qq
     * @return mixed
     */
    public function sendFlower($group, $qq)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('group', 'qq'));
    }

    /**
     * 发送群消息,可@指定用户
     * @param $group
     * @param string $msg
     * @param string $is_at
     * @return mixed
     */
    public function sendMsg($group, $msg = '', $is_at = '')
    {
        if ($is_at) {
            $msg .= $this->_core->atUser($is_at == 'all' ? '' : $is_at);
        }
        return $this->_core->callFunc('sendGroupMsg', compact('group', 'msg'));
    }

    /**
     * 加群申请
     * @param $responseFlag 事件标识
     * @param int $subType 请求类型 1群添加 群邀请
     * @param int $type 审核结果 1通过  2拒绝
     * @param string $msg 拒绝备注信息
     * @return mixed
     */
    public function setGroupAddRequest($responseFlag, $subType = 1, $type = 1, $msg = '')
    {
        return $this->_core->callFunc(__FUNCTION__, compact('responseFlag', 'subType','type','msg'));
    }

    /**
     * 设置群管理员（需要群主权限）
     * @param $group
     * @param $qq
     * @param bool $become
     * @return mixed
     */
    public function setGroupAdmin($group, $qq, $become = false)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('group', 'qq','become'));
    }

    /**
     * 设置群匿名聊天
     * @param $group
     * @param bool $open
     * @return mixed
     */
    public function setGroupAnonymous($group, $open = true)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('group', 'open'));
    }

    /**
     * 匿名群员禁言设置
     * @param $group
     * @param $anonymous
     * @param int $time
     * @return mixed
     */
    public function setGroupAnonymousBan($group, $anonymous, $time = 60)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('group', 'anonymous','time'));
    }

    /**
     * 指定群员禁言设置
     * @param $group
     * @param $qq
     * @param int $time
     * @return mixed
     */
    public function setGroupBan($group, $qq, $time = 60)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('group', 'qq','time'));
    }

    /**
     * 设置群名片（修改群用户名称）
     * @param $group
     * @param $qq
     * @param string $card
     * @return mixed
     */
    public function setGroupCard($group, $qq, $card = '')
    {
        return $this->_core->callFunc(__FUNCTION__, compact('group', 'qq','card'));
    }

    /**
     * 退出群聊
     * @param $group
     * @param bool $disband true/解散本群(群主) false/退出本群(管理、群成员)
     * @return mixed
     */
    public function setGroupLeave($group, $disband = false)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('group', 'disband'));
    }

    /**
     * 踢出群成员
     * @param $group
     * @param $qq
     * @param bool $refuseJoin true/不再接收此人加群申请，false/接收此人加群申请
     * @return mixed
     */
    public function setGroupKick($group, $qq, $refuseJoin = false)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('group', 'qq','refuseJoin'));
    }

    /**
     * 群签到
     * @param $group 0 签到所有群
     * @return mixed
     */
    public function setGroupSign($group = 0)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('group'));
    }

    /**
     * 置群成员专属头衔 - 需群主权限
     * @param $group
     * @param $qq
     * @param string $tip 头衔，可空。如果要删除，这里填空
     * @param int $time 专属头衔有效期，单位为秒，可空。如果永久有效，这里填写-1
     * @return mixed
     */
    public function setGroupSpecialTitle($group, $qq, $tip = '', $time = -1)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('group', 'qq','tip','time'));
    }

    /**
     * 设置群全员禁言
     * @param $group
     * @param bool $open true/开启禁言，false/关闭禁言
     * @return mixed
     */
    public function setGroupWholeBan($group, $open = true)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('group', 'open'));
    }

    /**
     * 获取匿名成员信息
     * @param $source
     * @return mixed
     */
    public function getAnonymousInfo($source)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('source'));
    }

    /**
     * 获取群内被禁言的用列表
     * @param $group
     * @return mixed
     */
    public function getBanList($group)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('group'));
    }

    /**
     * 获取群作业列表，默认取10条
     * @param $group
     * @param int $number
     * @return mixed
     */
    public function getGroupHomeworkList($group, $number = 10)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('group', 'number'));
    }

    /**
     * 获取群信息
     * @param $group
     * @return mixed
     */
    public function getGroupInfo($group)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('group'));
    }

    /**
     * 获取群链接，默认取10条
     * @param $group
     * @param int $number
     * @return mixed
     */
    public function getGroupLinkList($group, $number = 10)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('group', 'number'));
    }

    /**
     * 获取群列表
     * @return mixed
     */
    public function getGroupList()
    {
        return $this->_core->callFunc(__FUNCTION__);
    }

    /**
     * 获取群成员的信息
     * @param $group
     * @param $qq
     * @param int $cache
     * @return mixed
     */
    public function getGroupMemberInfo($group, $qq, $cache = 1)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('group', 'qq','cache'));
    }

    /**
     * 获取群内所有成员列表
     * @param $group
     * @return mixed
     */
    public function getGroupMemberList($group)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('group'));
    }

    /**
     * 获取群公告，默认取10条
     * @param $group
     * @param int $number
     * @return mixed
     */
    public function getGroupNoteList($group, $number = 10)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('group', 'number'));
    }

    /**
     * 获取置顶群公告
     * @param $group
     * @return mixed
     */
    public function getGroupTopNote($group)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('group'));
    }

    /**
     * 批量获取群头像，返回列表
     * @param array $groups
     * @return mixed
     */
    public function getMoreGroupHeadimg(Array $groups)
    {
        $groupList = implode('-', $groups);
        return $this->_core->callFunc(__FUNCTION__, compact('groupList'));
    }

    /**
     * 获取群文件列表
     * @param $group
     * @return mixed
     */
    public function getShareList($group)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('group'));
    }
}