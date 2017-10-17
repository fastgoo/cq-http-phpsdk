<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2017/10/15
 * Time: 下午12:04
 */

namespace CoolQ\Core;

use CoolQ\Lib\Redis;

class Receive
{
    private $config;

    private $_redis = null;

    public $data;

    const FRIEND_MSG_TYPE = 1;
    const FRIEND_MSG_FROM_ONLINE = 1;
    const FRIEND_MSG_FROM_GROUP = 2;
    const FRIEND_MSG_FROM_DISCUSS = 3;
    const FRIEND_MSG_FROM_FRIEND = 11;

    const FRIEND_ADDED_EVENT_TYPE = 201;
    const FRIEND_ADDED_EVENT_FROM_NORMAL = 1;

    const FRIEND_REQUEST_EVENT_TYPE = 301;
    const FRIEND_REQUEST_EVENT_FROM_NORMAL = 1;

    const GROUP_MSG_TYPE = 2;
    const GROUP_MSG_FROM_NORMAL = 1;
    const GROUP_MSG_FROM_ANONYMOUS = 2;
    const GROUP_MSG_FROM_SYSTEM = 3;

    const GROUP_FILE_UPLOAD_EVENT_TYPE = 11;
    const GROUP_FILE_UPLOAD_EVENT_FROM_NORMAL = 1;

    const GROUP_ADMIN_CHANGE_EVENT_TYPE = 101;
    const GROUP_ADMIN_CHANGE_EVENT_FROM_CANCEL = 1;
    const GROUP_ADMIN_CHANGE_EVENT_FROM_SET = 2;

    const GROUP_MEMBER_DECREASE_EVENT_TYPE = 102;
    const GROUP_MEMBER_DECREASE_EVENT_FROM_LEAVE = 1;
    const GROUP_MEMBER_DECREASE_EVENT_FROM_KICK = 2;

    const GROUP_MEMBER_INCREASE_EVENT_TYPE = 103;
    const GROUP_MEMBER_INCREASE_EVENT_FROM_AGREE = 1;
    const GROUP_MEMBER_INCREASE_EVENT_FROM_INVITE = 2;

    const GROUP_REQUEST_EVENT_TYPE = 302;
    const GROUP_REQUEST_EVENT_FROM_REQUEST = 1;
    const GROUP_REQUEST_FROM_INVITE = 2;

    const DISCUSS_MSG_TYPE = 4;
    const DISCUSS_MSG_FROM_NORMAL = 1;


    public function __construct($config)
    {
        $this->config = $config;
    }

    public function setRedis(Redis $redis)
    {
        $this->_redis = $redis;
    }

    /**
     * 反转义
     * @param string $msg 原消息，要反转义的字符串
     * @return string 反转义后的字符串
     */
    public function antiEscape($msg)
    {
        $msg = str_replace('&#91;', '[', $msg);
        $msg = str_replace('&#93;', ']', $msg);
        $msg = str_replace('&#44;', ',', $msg);
        $msg = str_replace('&amp;', '&', $msg);
        return $msg;
    }

    /**
     * 获取事件类型
     * @return mixed
     */
    public function getEventType()
    {
        return $this->data['type'];
    }

    /**
     * 获取事件来源类型
     * @return mixed
     */
    public function getEventFromType()
    {
        return $this->data['subType'];
    }

    /**
     * 接收酷Q传输的数据
     * @return array|mixed
     */
    public function getData()
    {
        $data = file_get_contents('php://input');
        $arr = json_decode(urldecode($data), true);
        if (!$arr) {
            $arr = $_POST;
            if (!$arr) {
                exit("无法获取到酷Q请求的数据");
            }
            isset($arr['fileInfo']) && $arr['fileInfo'] = json_decode($arr['fileInfo'], true);
            isset($arr['imageInfo']) && $arr['imageInfo'] = json_decode($arr['imageInfo'], true);
            isset($arr['anonymousInfo']) && $arr['anonymousInfo'] = json_decode($arr['anonymousInfo'], true);
        }
        if (!$this->checkKey($arr)) {
            exit("authToken校验失败，请核对信息");
        }
        $this->data = $arr;
        $this->_redis->setLog('result', json_encode($arr, JSON_UNESCAPED_UNICODE));
        return $this;
    }

    /**
     * 校验authToken的有效性
     * @param array $data
     * @return bool
     */
    private function checkKey(Array $data)
    {
        if (!$data) {
            return false;
        }
        if (!empty($this->config['key'])) {
            if (empty($data['authTime']) || empty($data['authToken'])) {
                return false;
            }
            return strtolower($data['authToken']) == md5("{$this->config['key']}:{$data['authTime']}");
        }
        return true;
    }

    /**
     * 匹配字符串，如果有则返回true
     * @param string $str
     * @return bool
     */
    public function parseStr($str = '')
    {
        if (empty($this->data['msg'])) {
            return false;
        }
        return strpos($this->data['msg'], "$str") !== false;
    }

    /**
     * 解析消息字符串，返回需要的指定数据
     * @param string $code
     * @param array $param
     * @return array|bool
     */
    private function parseMsg($code = '', $param = array())
    {
        if (empty($this->data['msg'])) {
            return false;
        }
        if (!$param) {
            return strpos($this->data['msg'], "[CQ:{$code}") !== false;
        }

        if (strpos($this->data['msg'], "[CQ:{$code}") !== false) {
            $data = [];
            $str = str_replace(
                array("[CQ:{$code},", "]"),
                "",
                in_array($code, ['image']) ? $this->data['originalMsg'] : $this->data['msg']);
            $arr = explode(',', $str);
            foreach ($arr as $value) {
                $a = explode('=', $value);
                if (count($a) == 2) {
                    if (in_array($a[0], $param)) {
                        $data[$a[0]] = $a[1];
                    }
                }
            }
        }
        return !empty($data) ? $data : false;
    }

    /**
     * 是否是红包，是返回数组
     * @return bool
     */
    public function isRedReward()
    {
        return $this->parseMsg('hb', ['title']);
    }

    /**
     * 是否是@我，是返回消息字符串数据
     * @return bool|mixed
     */
    public function isAtMe()
    {
        if (empty($this->data['msg'])) {
            return false;
        }
        if (strpos($this->data['msg'], "[CQ:at,qq={$this->data['loginQQ']}") !== false) {
            $data['msg'] = str_replace(array("[CQ:at,qq={$this->data['loginQQ']}]"), "", $this->data['msg']);
        }
        return !empty($data) ? $data : false;
    }

    /**
     * 是否是图片，是则返回图片数组
     */
    public function isImage()
    {
        return !empty($this->data['imageInfo']) && is_array($this->data['imageInfo']) ? $this->data['imageInfo'] : false;
    }

    /**
     * 是否是分享链接，是则返回分享链接数组
     * @return array|bool
     */
    public function isUrlShare()
    {
        if (empty($this->data['msg'])) {
            return false;
        }
        if (strpos($this->data['msg'], "[CQ:rich") !== false) {
            $result = [];
            $data['msg'] = str_replace(array("[CQ:rich,","]"), "", $this->data['msg']);
            foreach (explode(',',$data['msg']) as $val){
                if($url = strstr($val, "url=")){
                    $result['url'] = $url;
                    $result['url'] = mb_substr($result['url'],4);
                }
                if($text = strstr($val, "text=")){
                    $result['text'] = str_replace(["\n","text="],"",$text);
                    $result['title'] = strstr(str_replace("\n ","",$text), ' ', true);
                    $result['title'] = mb_substr($result['title'],5);
                    $result['desc'] = strstr(str_replace(["\n ","\n"],"",$text), ' ');
                    $result['desc'] = mb_substr($result['desc'],1);
                }
            }
        }
        return !empty($result) ? $result : false;
    }

    /**
     * 是否是抖动信息
     * @return bool
     */
    public function isShake()
    {
        return $this->parseMsg('shake');
    }

    /**
     *
     * @return bool
     */
    public function isFace()
    {
        return $this->parseMsg('bface') || $this->parseMsg('face') || $this->parseMsg('emoji');
    }

    /**
     * 是否是分享名片
     * @return bool
     */
    public function isShareCard()
    {
        return $this->parseMsg('contact', ['id', 'type']);
    }

    /**
     * 是否是匿名消息
     * @return bool
     */
    public function isAnonymous()
    {
        return $this->parseMsg('anonymous');
    }

    /**
     * 是否是定位分享，是则返回定位数组
     * @return array|bool
     */
    public function isLocation()
    {
        return $this->parseMsg('location', ['lat', 'lon', 'zoom', 'title', 'content']);
    }

    /**
     * 是否是音乐，是则返回数组
     * @return array|bool
     */
    public function isMusic()
    {
        return $this->parseMsg('music', ['id', 'type', 'style', 'type', 'url', 'audio']);
    }

    /**
     * 是否是语音，返回数组
     * @return array|bool
     */
    public function isRecord()
    {
        return $this->parseMsg('record', ['file']);
    }

    /**
     * 是否是米show，范湖数组
     * @return array|bool
     */
    public function isMiShow()
    {
        return $this->parseMsg('show', ['id', 'qq', 'content']);
    }

}