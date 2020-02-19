<?php

namespace App\Common\Model\CacheModel;

use Library\Virtual\Model\CacheModel\AbstractRedisModel;

/**
 * Created by PhpStorm.
 * User: ZhongHao-Zh
 * Date: 2019/10/26
 * Time: 20:18
 */
class ReplyGiftModel extends AbstractRedisModel
{
    private function getReplyGiftRedisKey($appid)
    {
        return "{$appid}_gift_reply";
    }

    public function getReplyGift($appid)
    {
        return unserialize($this->redis->get($this->getReplyGiftRedisKey($appid)));
    }

    public function setReplyGift($appid, $replyList)
    {
        $this->redis->set($this->getReplyGiftRedisKey($appid), serialize($replyList));
    }
}