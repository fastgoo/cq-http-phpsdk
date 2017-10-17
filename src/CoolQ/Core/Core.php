<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2017/10/14
 * Time: 下午3:25
 */

namespace CoolQ\Core;

use CoolQ\Lib\Redis;

class Core
{

    /**
     * 配置信息
     * @var string
     */
    public $config = [];

    private $_redis = null;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function setRedis(Redis $redis)
    {
        $this->_redis = $redis;
    }

    /**
     * 访问网页
     * @param string $url 请求网址
     * @param string $data 请求数据，非空时使用POST方法
     * @param string $cookies 可空
     * @param array $headers
     * @param string $proxy 代理地址，可空
     * @param int $time 超时时间，单位：秒。默认10秒
     * @return string 执行结果
     */
    protected function getHttpData($url = '', $data = '', $cookies = '', $headers = array(), $proxy = '', $time = 8)
    {
        $ch = curl_init($url); //初始化 CURL 并设置请求地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //设置获取的信息以文件流的形式返回
        if ($data) curl_setopt($ch, CURLOPT_POST, 1); //设置 post 方式提交
        if ($data) curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //设置 post 数据
        if (is_array($cookies) && $cookies) {
            foreach ($cookies as $array) $data .= $array;
            $cookies = $data;
        }
        if ($cookies) curl_setopt($ch, CURLOPT_COOKIE, $cookies);   //设置Cookies
        if ($headers) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($proxy) curl_setopt($ch, CURLOPT_USERAGENT, $proxy);
        curl_setopt($ch, CURLOPT_TIMEOUT, $time);   //只需要设置一个秒的数量就可以
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在

        $data = curl_exec($ch); //执行命令
        curl_close($ch); //关闭 CURL

        return $data;
    }

    /**
     * 转义
     * @param string $msg 要转义的字符串
     * @param boolean $escapeComma 转义逗号，默认不转义
     * @return string 转义后的字符串
     */
    public function escape($msg, $escapeComma = false)
    {
        $msg = str_replace('[', '&#91;', $msg);
        $msg = str_replace(']', '&#93;', $msg);
        $msg = str_replace('&', '&amp;', $msg);
        if ($escapeComma) $msg = str_replace(',', '&#44;', $msg);
        return $msg;
    }

    /**
     * 发送数据
     * @param array $request
     * @return mixed
     */
    public function send(Array $request)
    {
        if (!$request) {
            return false;
        }

        if ($this->config['key']) {
            $request['authTime'] = time();
            $request['authToken'] = md5($this->config['key'] . ':' . $request['authTime']);
        }

        try {
            $response = $this->getHttpData(
                $this->config['url'],
                json_encode($request),
                '',
                '',
                '',
                $this->config['timeout']
            );
            $this->_redis->setLog('request',json_encode($request, JSON_UNESCAPED_UNICODE));
            if (!$response) {
                throw new \Exception('请求服务器失败');
            }
            $result = json_decode($response, true);
            if (!$result) {
                throw new \Exception('服务器返回数据错误');
            }
            if (!empty($result['request'])) {
                unset($result['request']);
            }
            $this->_redis->setLog('response',json_encode($result, JSON_UNESCAPED_UNICODE));
            return $result;
        } catch (\Exception $exception) {
            $this->_redis->setLog('response',$exception->getMessage());
            return false;
        }
    }

    /**
     * 调用酷Q插件方法
     * @param $method
     * @param array $param
     * @return mixed
     */
    public function callFunc($method, $param = array())
    {
        if ($param) {
            $param['fun'] = $method;
            $request = $param;
        } else {
            $request = ['fun' => $method];
        }
        return $this->send($request);
    }

    /**
     * 转化为酷Q运行码
     * @param string $qq
     * @return string
     */
    public function atUser($qq = '')
    {
        return "[CQ:at,qq=" . ($qq ?: 'all') . "]";
    }

    /**
     * 转化为emoji标签
     * @param $id
     * @return string
     */
    public function cqEmoji($id)
    {
        return "[CQ:emoji,id=$id]";
    }

    /**
     * 转化为收藏表情
     * @param $id
     * @return string
     */
    public function cqFace($id)
    {
        return "[CQ:face,id=$id]";
    }

    /**
     * 取CQ码_大表情(bface)
     * @param int $pID 大表情所属系列的标识
     * @param int $id 大表情的唯一标识
     * @return string CQ码_大表情
     */
    public function cqBigFace($pID, $id)
    {
        return "[CQ:bface,p=$pID,id=$id]";
    }

    /**
     * 取CQ码_小表情(sface)
     * @param int $id 小表情代码
     * @return string CQ码_小表情
     */
    public function cqSmallFace($id)
    {
        return "[CQ:sface,id=$id]";
    }

    /**
     * 抖一抖，针对好友有效
     * @return string
     */
    public function cqShake()
    {
        return '[CQ:shake]';
    }

    /**
     * 转换为分享链接酷Q识别码
     * @param $url
     * @param string $title
     * @param string $content
     * @param string $picUrl
     * @return string
     */
    public function urlShare($url, $title = '', $content = '', $picUrl = '') //发送链接分享
    {

        $msg = '[CQ:share,url=' . $this->escape($url, true);
        if ($title) {
            $msg .= ',title=' . $this->escape($title, true);
        }
        if ($content) {
            $msg .= ',content=' . $this->escape($content, true);
        }
        if ($picUrl) {
            $msg .= ',image=' . $this->escape($picUrl, true);
        }
        return $msg . ']';
    }

    /**
     * 分享名片，酷Q识别码
     * @param string $type
     * @param $id
     * @return string
     */
    public function cardShare($type = 'qq', $id)
    {
        $type = $this->escape($type, true);
        return "[CQ:contact,type=$type,id=$id]";
    }

    /**
     * 开启匿名聊天
     * @param bool $ignore
     * @return string
     */
    public function cqAnonymous($ignore = false)
    {
        return $ignore ? '[CQ:anonymous,ignore=true]' : '[CQ:anonymous]';
    }

    /**
     * CQ码_图片(image)
     * @param string $path 图片路径，可使用网络图片和本地图片．使用本地图片时需在路径前加入 file://
     * @return string CQ码_图片
     */
    public function cqImage($path)
    {
        $path = $this->escape($path, true);
        return "[CQ:image,file=$path]";
    }

    /**
     * 取CQ码_位置分享(location)
     * @param double $lat 纬度
     * @param double $lon 经度
     * @param int $zoom 放大倍数，可空，默认为 15
     * @param string $title 地点名称，建议12字以内
     * @param string $content 地址，建议20字以内
     * @return string CQ码_位置分享
     */
    public function cqLocation($lat, $lon, $title, $content, $zoom = 15)
    {
        $title = $this->escape($title, true);
        $content = $this->escape($content, true);
        return "[CQ:location,lat=$lat,lon=$lon,zoom=$zoom,title=$title,content=$content]";
    }

    /**
     * 取CQ码_音乐(music)
     * @param number $songID 音乐的歌曲数字ID
     * @param string $type 音乐网站类型，目前支持 qq/QQ音乐 163/网易云音乐 xiami/虾米音乐，默认为qq
     * @param bool $newStyle 是否启用新版样式，目前仅 QQ音乐 支持
     * @return string CQ码_音乐
     */
    public function cqMusic($songID, $type = 'qq', $newStyle = false) //发送音乐
    {
        $type = $this->escape($type, true);
        $newStyle = $newStyle ? 1 : 0;
        return "[CQ:music,id=$songID,type=$type,style=$newStyle]";
    }

    /**
     * 取CQ码_音乐自定义分享(music)
     * @param string $url 分享链接，点击分享后进入的音乐页面（如歌曲介绍页）
     * @param string $audio 音频链接，音乐的音频链接（如mp3链接）
     * @param string $title 标题，可空，音乐的标题，建议12字以内
     * @param string $content 内容，可空，音乐的简介，建议30字以内
     * @param string $image 封面图片链接，可空，音乐的封面图片链接，留空则为默认图片
     * @return string CQ码_音乐自定义分享
     */
    public function cqCustomMusic($url, $audio, $title = '', $content = '', $image = '') //发送自定义音乐分享
    {
        $url = $this->escape($url, true);
        $audio = $this->escape($audio, true);
        $para = "[CQ:music,type=custom,url=$url,audio=$audio";
        if ($title) $para .= ',title=' . $this->escape($title, true);
        if ($content) $para .= ',content=' . $this->escape($content, true);
        if ($image) $para .= ',image=' . $this->escape($image, true);
        return $para . ']';
    }

    /**
     * 取CQ码_语音(record)
     * @param string $path 语音路径，可使用网络和本地语音文件．使用本地语音文件时需在路径前加入 file://
     * @return string CQ码_语音
     */
    public function cqRecord($path)
    {
        $path = $this->escape($path, true);
        return "[CQ:record,file=$path]";
    }

    /**
     * 取CQ码_厘米秀(show)
     * @param int $id 动作代码
     * @param number $qq 动作对象，可空，仅在双人动作时有效
     * @param string $content 消息内容，建议8个字以内
     * @return string CQ码_厘米秀
     */
    public function cqShow($id, $qq = null, $content = '')
    {
        $msg = '[CQ:show,id=' . $id;
        if ($qq) $msg .= ',qq=' . $qq;
        if ($content) $msg .= ',content=' . $this->escape($content, true);
        return $msg . ']';
    }
}