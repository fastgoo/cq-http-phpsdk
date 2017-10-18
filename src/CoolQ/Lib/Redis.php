<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2017/10/16
 * Time: 下午4:02
 */

namespace CoolQ\Lib;

class Redis
{
    private $_redis;

    private $is_connect = false;

    private $config;

    /**
     * 初始化redis连接
     * Redis constructor.
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->config = $config;
        if (extension_loaded('redis') && !empty($this->config['host'])) {
            $this->_redis = new \Redis();
            $this->is_connect = $this->_redis->connect($this->config['host'], $this->config['port'] ?: 6379);
            if ($this->is_connect && $this->config['auth']) {
                $this->_redis->auth($this->config['auth']);
            }
        }
    }

    /**
     * 获取日志
     * @return bool|string
     */
    public function getLog()
    {
        return $this->_redis->get($this->config['logKey']);
    }

    /**
     * 清空日志记录
     */
    public function deleteLog()
    {
        return $this->_redis->delete($this->config['logKey']);
    }

    /**
     * 获取redis实例
     * @return bool|\Redis
     */
    public function getRedis()
    {
        if (!$this->is_connect) {
            return false;
        }
        return $this->_redis;
    }

    /**
     * 写入Log
     * @param string $type
     * @param string $message
     * @return bool
     */
    public function setLog($type = 'result', $message = '')
    {
        if (!$this->is_connect) {
            return false;
        }

        $key = $this->config['logKey'] ?: 'coolq-log';
        $logStr = $this->_redis->get($key);
        $date = date('Y/m/d H-i-s');
        switch ($type) {
            case 'result':
                $typeName = '接收酷Q请求数据';
                break;
            case 'request':
                $typeName = '请求酷Q数据';
                break;
            case 'response':
                $typeName = '返回数据';
                break;
            default:
                $typeName = 'debug';
        }
        return $this->_redis->set($key, $logStr . "\n<br>" . $date . "[$typeName]: " . $message);
    }
}