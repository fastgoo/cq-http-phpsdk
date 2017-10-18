<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2017/10/18
 * Time: 下午2:11
 */
require __DIR__ . '/vendor/autoload.php';
require 'config.php';

use CoolQ\Core\CoolQ;
use Handler\Group;
use Handler\User;
use Handler\Discuss;


$cq = new CoolQ($config);
$receive = $cq->receive->getData();
$userHandler = new User($cq);
$groupHandler = new Group($cq);
$discussHandler = new Discuss($cq);

switch ($receive->getEventType()) {
    case $receive::FRIEND_MSG_TYPE:
        $userHandler->onMessage();
        break;
    case $receive::FRIEND_REQUEST_EVENT_TYPE:
        $userHandler->onRequest();
        break;
    case $receive::GROUP_MSG_TYPE:
        $groupHandler->onMessage();
        break;
    case $receive::DISCUSS_MSG_TYPE:
        $discussHandler->onMessage();
        break;
    case $receive::GROUP_REQUEST_EVENT_TYPE:
        $groupHandler->onRequest();
        break;
    case $receive::GROUP_FILE_UPLOAD_EVENT_TYPE:
        break;
    case $receive::GROUP_ADMIN_CHANGE_EVENT_TYPE:
        $groupHandler->onAdminChange();
        break;
    case $receive::GROUP_MEMBER_DECREASE_EVENT_TYPE:
        $groupHandler->onMemberChange(2);
        break;
    case $receive::GROUP_MEMBER_INCREASE_EVENT_TYPE:
        $groupHandler->onMemberChange(1);
        break;
    default:
        break;
}