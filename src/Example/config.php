<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2017/10/18
 * Time: 下午2:12
 */
$config = [
    /** 酷Q IP地址 */
    'host' => '',
    /** 酷Q 监听端口 */
    'port' => '',
    /** 验证Key */
    'key' => '',
    /** 数据有效时间 */
    'timeout' => 30,
    /** redis配置 */
    'redis' => [
        'host' => '',
        'port' => '',
        'auth' => '',
        /** 日志key */
        'logKey' => 'coolq-log',
    ],
];
$config['url'] = $config['host'] . ':' . (!empty($_GET['port']) ? $_GET['port'] : $config['port']);
