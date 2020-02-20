<?php

namespace App\Common\Model\CacheModel;

use Library\Virtual\Model\CacheModel\AbstractRedisModel;

/**
 * Created by PhpStorm.
 * User: ZhongHao-Zh
 * Date: 2019/10/26
 * Time: 20:18
 */
class EventQuestionModel extends AbstractRedisModel
{
    public function getEventQuestionRedisKey($appId)
    {
        return "{$appId}_event_build";
    }

    public function getEventQuestion($appId)
    {
        return unserialize($this->redis->get($this->getEventQuestionRedisKey($appId)));
    }

    public function setEventQuestion($appId, $eventQuestionList)
    {
        $this->redis->set($this->getEventQuestionRedisKey($appId), serialize($eventQuestionList));
    }
}