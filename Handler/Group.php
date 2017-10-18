<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2017/10/16
 * Time: 上午11:18
 */

namespace Handler;

use CoolQ\Core\CoolQ;
use Service\Music163;

class Group
{
    private $cq;
    private $receive;

    public function __construct(CoolQ $cq)
    {
        $this->cq = $cq;
        $this->receive = $cq->receive;
    }

    public function onMessage()
    {
        $this->testReceive();
        $msg = $this->receive->isAtMe();
        if(!$msg){
            //return [];
        }
        if ($this->receive->parseStr('图片测试')) {
            $url = "https://gchat.qpic.cn/gchatpic_new/773729704/659045538-2712952978-3669C3C024A39FCDE93CC6A07E377746/0?vuin=3340177994&term=2";
            $msg = $this->cq->core->cqImage($url);
            return $this->cq->group->sendMsg($this->receive->data['group'], $msg);
        } else if ($this->receive->parseStr('语音测试')) {
            $url = "http://hao.1015600.com/upload/ring/000/989/e4ed9bd57cee72b7d994b7613719b23a.mp3";
            $msg = $this->cq->core->cqRecord($url);
            return $this->cq->group->sendMsg($this->receive->data['group'], $msg);
        } else if ($this->receive->parseStr('表情测试')) {
            $msg = $this->cq->core->cqEmoji(1);
            $msg .= $this->cq->core->cqFace(1);
            //$msg .= $this->cq->group->cqBigFace(1);
            $msg .= $this->cq->core->cqSmallFace(1);
            return $this->cq->group->sendMsg($this->receive->data['group'], $msg);
        } else if ($this->receive->parseStr('分享链接测试')) {
            $url = "http://www.baidu.com";
            $picUrl = "https://gchat.qpic.cn/gchatpic_new/773729704/659045538-2712952978-3669C3C024A39FCDE93CC6A07E377746/0?vuin=3340177994&term=2";
            $msg = $this->cq->core->urlShare($url, '测试链接标题', '测试链接内容666', $picUrl);
            return $this->cq->group->sendMsg($this->receive->data['group'], $msg);
        } else if ($this->receive->parseStr('分享名片测试')) {
            $msg = $this->cq->core->cardShare('qq', 773729704);
            return $this->cq->group->sendMsg($this->receive->data['group'], $msg);
        } else if ($this->receive->parseStr('分享位置测试')) {
            $msg = $this->cq->core->cqLocation(0, 0, '位置测试标题', '位置测试内容');
            return $this->cq->group->sendMsg($this->receive->data['group'], $msg);
        } else if ($this->receive->parseStr('分享音乐测试')) {
            $url = "http://www.baidu.com";
            $audoUrl = "http://hao.1015600.com/upload/ring/000/989/e4ed9bd57cee72b7d994b7613719b23a.mp3";
            $picUrl = "https://gchat.qpic.cn/gchatpic_new/773729704/659045538-2712952978-3669C3C024A39FCDE93CC6A07E377746/0?vuin=3340177994&term=2";
            $msg = $this->cq->core->cqCustomMusic($url, $audoUrl, '音乐测试标题', '音乐测试内容', $picUrl);
            return $this->cq->group->sendMsg($this->receive->data['group'], $msg);
        }else if($this->receive->parseStr('获取音乐')){
            $music = new Music163();
            $str = str_replace(["\n"," ","获取音乐"],"",$msg['msg']);
            $musicInfo = $music->music_search($str,1)->getShareInfo();
            if(!$musicInfo){
                return $this->cq->group->sendMsg($this->receive->data['group'], '无法搜索到 '.$str.' 音乐！',$this->receive->data['qq']);
            }
            $userInfo = $this->cq->user->getStrangerInfo($this->receive->data['qq'], false);
            $userInfo = $userInfo['result'];
            $musicInfo['desc'] .= "\n(用户：{$userInfo['name']} 点播)";
            $msg = $this->cq->core->urlShare($musicInfo['url'], $musicInfo['title'], $musicInfo['desc'], $musicInfo['picUrl']);
            return $this->cq->group->sendMsg($this->receive->data['group'], $msg);
        }
        return [];
    }

    public function testReceive()
    {
        if($res = $this->receive->isRedReward()){
            $this->cq->redis->setLog('','红包->'.json_encode($res,JSON_UNESCAPED_UNICODE));
        }
        if($res = $this->receive->isAtMe()){
            $this->cq->redis->setLog('','@我->'.json_encode($res,JSON_UNESCAPED_UNICODE));
        }
        if($res = $this->receive->isImage()){
            $this->cq->redis->setLog('','图片->'.json_encode($res,JSON_UNESCAPED_UNICODE));
        }
        if($res = $this->receive->isUrlShare()){
            $this->cq->redis->setLog('','分享链接->'.json_encode($res,JSON_UNESCAPED_UNICODE));
        }
        if($res = $this->receive->isShake()){
            $this->cq->redis->setLog('','抖一抖->'.json_encode($res,JSON_UNESCAPED_UNICODE));
        }
        if($res = $this->receive->isFace()){
            $this->cq->redis->setLog('','表情->'.json_encode($res,JSON_UNESCAPED_UNICODE));
        }
        if($res = $this->receive->isShareCard()){
            $this->cq->redis->setLog('','分享名片->'.json_encode($res,JSON_UNESCAPED_UNICODE));
        }
        if($res = $this->receive->isAnonymous()){
            $this->cq->redis->setLog('','匿名聊天->'.json_encode($res,JSON_UNESCAPED_UNICODE));
        }
        if($res = $this->receive->isLocation()){
            $this->cq->redis->setLog('','分享定位->'.json_encode($res,JSON_UNESCAPED_UNICODE));
        }
        if($res = $this->receive->isMusic()){
            $this->cq->redis->setLog('','分享音乐->'.json_encode($res,JSON_UNESCAPED_UNICODE));
        }
        if($res = $this->receive->isRecord()){
            $this->cq->redis->setLog('','语音->'.json_encode($res,JSON_UNESCAPED_UNICODE));
        }
        if($res = $this->receive->isMiShow()){
            $this->cq->redis->setLog('','厘米秀->'.json_encode($res,JSON_UNESCAPED_UNICODE));
        }
    }

    public function onRequest()
    {
        $this->cq->group->setGroupAddRequest($this->receive->data['responseFlag'], $this->receive->getEventFromType(), 1);
    }

    public function onAdminChange()
    {
        $userInfo = $this->cq->user->getStrangerInfo($this->receive->data['beingOperateQQ'], false);
        $userInfo = $userInfo['result'];
        $receive = $this->receive;
        if ($this->receive->getEventFromType() == $receive::GROUP_ADMIN_CHANGE_EVENT_FROM_CANCEL) {
            $msg = "用户 {$userInfo['name']} 失去管理员资格";
        } else {
            $msg = "恭喜 {$userInfo['name']} 成为管理员";
        }
        $this->cq->group->sendMsg($receive->data['group'], $msg);
    }

    public function onMemberChange($type = 1)
    {
        $userInfo = $this->cq->user->getStrangerInfo($this->receive->data['beingOperateQQ'], false);
        $userInfo = $userInfo['result'];
        $this->cq->group->sendMsg($this->receive->data['group'], '用户 ' . $userInfo['name'] . ($type == 1 ? ' 加入群聊' : ' 离开群聊'));
    }

}