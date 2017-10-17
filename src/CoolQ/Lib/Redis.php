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

    private $_key;

    private $is_connect = false;

    public function __construct($config = array())
    {
        if (extension_loaded('redis')) {
            $this->_redis = new \Redis();
            $host = !empty($config['host']) ? $config['host'] : '39.108.134.88';
            $port = !empty($config['port']) ? $config['port'] : 6379;
            $auth = !empty($config['auth']) ? $config['auth'] : 'Mr.Zhou';
            $this->is_connect = $this->_redis->connect($host, $port);
            $this->is_connect && $this->_redis->auth($auth);
        }
    }

    public function setLogKey($key)
    {
        $this->_key = $key;
    }

    public function getRedis()
    {
        if (!$this->is_connect) {
            return false;
        }
        return $this->_redis;
    }

    public function setLog($type = 'result', $message = '')
    {
        if (!$this->is_connect) {
            return false;
        }

        $key = $this->_key ?: 'coolq-log';
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