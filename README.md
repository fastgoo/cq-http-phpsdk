项目介绍
=======
基于酷Q机器人cq-http扩展插件开发的PHPSDK，可以通过web管理群、QQ。可接收QQ消息事件，请求事件等等；

Composer 安装
------------
```bash
新建composer.json文件，引入包如下：
 
{
  "require-dev": {
    "fastgoo/cq-http-phpsdk": "dev-master"
  }
} 

composer install
```

依赖扩展
-------
1、日志系统依赖 Redis扩展 和 Redis服务器

项目结构
-------

```bash
src/
    CoolQ/
        Core/
            CoolQ.php  核心管理器，管理Core、Discuss、Group、Receive、System、User这些核心类
            Core.php 核心类 HTTP请求、酷Q码转换、请求服务器数据做拼装、Redis日志记录
            Discuss.php 讨论组API
            Group.php 群组API
            User.php 用户API
            System.php 系统API
            Receive.php 接收酷Q请求的数据，数据转化、校验
        Lib -
            Redis.php Redis日志类
```

使用教程
-------
```
<?php
 
require __DIR__ . '/vendor/autoload.php';

use CoolQ\Core\CoolQ;

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

/** 初始化核心类 */
$cq = new CoolQ($config);
/** 获取酷Q请求数据 */
$cq->receive->getData();
/** 接收实例 */
$receive = $cq->receive;

/** 事件类型操作 */
switch ($receive->getEventType()) {
    case $receive::FRIEND_MSG_TYPE: //用户消息
        $cq->user->sendMsg($receive->data['qq'],'收到消息->'.$receive->data['msg']);
        break;
    case $receive::FRIEND_REQUEST_EVENT_TYPE: //收到好友请求
        $cq->user->setFriendAddRequest($receive->data['responseFlag'], 1);
        $cq->user->sendMsg($receive->data['qq'], '很高兴我们成为朋友，我是酷Q机器人');
        break;
    case $receive::GROUP_MSG_TYPE: //群消息
        $cq->group->sendMsg($receive->data['group'],'收到群消息->'.$receive->data['msg']);
        break;
    case $receive::DISCUSS_MSG_TYPE: //讨论组消息
        $cq->discuss->sendMsg($receive->data['group'],'收到群消息->'.$receive->data['msg']);
        break;
    case $receive::GROUP_REQUEST_EVENT_TYPE: //加群申请
        $cq->group->setGroupAddRequest($receive->data['responseFlag'], $receive->getEventFromType(), 1);
        break;
    case $receive::GROUP_FILE_UPLOAD_EVENT_TYPE: //群文件变动
        break;
    case $receive::GROUP_ADMIN_CHANGE_EVENT_TYPE: //管理员变更
        if ($receive->getEventFromType() == $receive::GROUP_ADMIN_CHANGE_EVENT_FROM_CANCEL) {
            $msg = " 失去管理员资格";
        } else {
            $msg = " 成为管理员";
        }
        $cq->group->sendMsg($receive->data['group'], $receive->data['beingOperateQQ'].$msg);
        break;
    case $receive::GROUP_MEMBER_DECREASE_EVENT_TYPE: //群人员减少
        $cq->group->sendMsg($receive->data['group'], $receive->data['beingOperateQQ'].'离开群');
        break;
    case $receive::GROUP_MEMBER_INCREASE_EVENT_TYPE: //群人员增加
        $cq->group->sendMsg($receive->data['group'], $receive->data['beingOperateQQ'].'加入群');
        break;
    default:
        break;
}
```


作者
-------
QQ：773729704 记得备注github

微信：huoniaojugege  记得备注github

需要做web QQ机器人管理系统的可以联系我