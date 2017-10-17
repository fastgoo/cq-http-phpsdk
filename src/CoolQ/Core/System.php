<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2017/10/14
 * Time: 下午5:30
 */

namespace CoolQ\Core;

class System
{

    private $_core;

    public function __construct(Core $core)
    {
        $this->_core = $core;
    }

    /**
     * 获取酷Q版本信息
     */
    public function getVersion()
    {
        return $this->_core->callFunc(__FUNCTION__);
    }

    /**
     * 添加酷Q执行日志
     * 0/调试，10/信息，11/信息_成功，12/信息_接收，13/信息_发送，20/警告，30/错误，40/致命错误
     * @param string $type
     * @param string $text
     * @param int $level
     * @return mixed
     */
    public function addLog($type='',$text='',$level=0)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('level', 'type','text'));
    }


    /**
     * 重启服务器，如果服务器关闭了
     */
    public function rebootService()
    {
        return $this->_core->callFunc(__FUNCTION__);
    }

    /**
     * 获取酷Q的运行状态
     * @return mixed
     */
    public function getRunStatus()
    {
        return $this->_core->callFunc(__FUNCTION__);
    }

    /**
     * 下载文件到酷Q
     * @param string $url
     * @param string $name
     * @param int $type 1图片 2语音
     * @return mixed
     */
    public function downFile($url='',$name='',$type = 1)
    {
        $md5 = '';
        return $this->_core->callFunc(__FUNCTION__, compact('url', 'name','type','md5'));
    }

    /**
     * 接收语音文件
     * @param string $name
     * @param string $format
     * @param bool $needFile
     * @return mixed
     */
    public function getRecord($name='',$format='mp3',$needFile=true)
    {
        $source = $name;
        return $this->_core->callFunc(__FUNCTION__, compact('source', 'format','type','needFile'));
    }

    /**
     * 接收图片文件
     * @param string $name
     * @param bool $needFile
     * @return mixed
     */
    public function getImageInfo($name='',$needFile=true)
    {
        $source = $name;
        return $this->_core->callFunc(__FUNCTION__, compact('source', 'needFile'));
    }

    /**
     * 接收文件信息
     * @param string $name
     * @return mixed
     */
    public function getFileInfo($name='')
    {
        $source = $name;
        return $this->_core->callFunc(__FUNCTION__, compact('source'));
    }

    /**
     * 设置悬浮窗数据
     * @param $data 数据内容
     * @param string $unit 数据单位
     * @param int $color 1/绿 2/橙 3/红 4/深红 5/黑 6/灰
     * @return mixed
     */
    public function setStatus($data, $unit = 's', $color = 1)
    {
        return $this->_core->callFunc(__FUNCTION__, compact('data','unit','color'));
    }
}