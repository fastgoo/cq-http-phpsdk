<?php
/**
 * Created by PhpStorm.
 * User: Mr.Zhou
 * Date: 2017/10/16
 * Time: 上午11:18
 */

namespace Handler;

use CoolQ\Core\CoolQ;

class User
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
        if ($this->receive->parseStr('图片测试')) {
            $url = "https://gchat.qpic.cn/gchatpic_new/773729704/659045538-2712952978-3669C3C024A39FCDE93CC6A07E377746/0?vuin=3340177994&term=2";
            $msg = $this->cq->core->cqImage($url);
            return $this->cq->user->sendMsg($this->receive->data['qq'], $msg);
        } else if ($this->receive->parseStr('语音测试')) {
            $url = "http://hao.1015600.com/upload/ring/000/989/e4ed9bd57cee72b7d994b7613719b23a.mp3";
            $msg = $this->cq->core->cqRecord($url);
            return $this->cq->user->sendMsg($this->receive->data['qq'], $msg);
        } else if ($this->receive->parseStr('表情测试')) {
            $msg = $this->cq->core->cqEmoji(1);
            $msg .= $this->cq->core->cqFace(1);
            //$msg .= $this->cq->user->cqBigFace(1);
            $msg .= $this->cq->core->cqSmallFace(1);
            return $this->cq->user->sendMsg($this->receive->data['qq'], $msg);
        } else if ($this->receive->parseStr('抖一抖测试')) {
            $msg = $this->cq->core->cqShake();
            return $this->cq->user->sendMsg($this->receive->data['qq'], $msg);
        } else if ($this->receive->parseStr('分享链接测试')) {
            $url = "http://www.baidu.com";
            $picUrl = "https://gchat.qpic.cn/gchatpic_new/773729704/659045538-2712952978-3669C3C024A39FCDE93CC6A07E377746/0?vuin=3340177994&term=2";
            $msg = $this->cq->core->urlShare($url, '测试链接标题', '测试链接内容666', $picUrl);
            return $this->cq->user->sendMsg($this->receive->data['qq'], $msg);
        } else if ($this->receive->parseStr('分享名片测试')) {
            $msg = $this->cq->core->cardShare('qq', 773729704);
            return $this->cq->user->sendMsg($this->receive->data['qq'], $msg);
        } else if ($this->receive->parseStr('分享位置测试')) {
            $msg = $this->cq->core->cqLocation(0, 0, '位置测试标题', '位置测试内容');
            return $this->cq->user->sendMsg($this->receive->data['qq'], $msg);
        } else if ($this->receive->parseStr('分享音乐测试')) {
            $url = "http://www.baidu.com";
            $audoUrl = "http://hao.1015600.com/upload/ring/000/989/e4ed9bd57cee72b7d994b7613719b23a.mp3";
            $picUrl = "https://gchat.qpic.cn/gchatpic_new/773729704/659045538-2712952978-3669C3C024A39FCDE93CC6A07E377746/0?vuin=3340177994&term=2";
            $msg = $this->cq->core->cqCustomMusic($url, $audoUrl, '音乐测试标题', '音乐测试内容', $picUrl);
            return $this->cq->user->sendMsg($this->receive->data['qq'], $msg);
        } else if ($this->receive->parseStr('厘米秀测试')) {
            $msg = $this->cq->core->cqShow(1, $this->receive->data['qq'], '厘米秀测试内容');
            return $this->cq->user->sendMsg($this->receive->data['qq'], $msg);
        }
        return [];
    }

    public function onRequest()
    {
        $this->cq->user->setFriendAddRequest($this->receive->data['responseFlag'], 1);
        return $this->cq->user->sendMsg($this->receive->data['qq'], '很高兴我们成为朋友，我是酷Q机器人');
    }

    public function onEvent()
    {
        $this->receive->getEventFromType();
    }

}