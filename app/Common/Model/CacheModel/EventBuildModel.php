<?php

namespace App\Common\Model\CacheModel;

use Illuminate\Support\Collection;
use Library\Virtual\Model\CacheModel\AbstractRedisModel;

/**
 * Created by PhpStorm.
 * User: ZhongHao-Zh
 * Date: 2019/10/26
 * Time: 20:18
 */
class EventBuildModel extends AbstractRedisModel
{
    private function getEventBuildRedisKey($appId)
    {
        return "{$appId}_event_build";
    }

    public function getEventBuild($appId)
    {
        return unserialize($this->redis->get($this->getEventBuildRedisKey($appId)));
    }

    public function setEventBuild($appId, $eventBuildList)
    {
        $this->redis->set($this->getEventBuildRedisKey($appId), serialize($eventBuildList));
    }

    public function getNowFloorRedisKey($eventKey)
    {
        return "{$eventKey}_floor_number";
    }

    public function getNowFloor($eventKey)
    {
        $this->redis->get($this->getNowFloorRedisKey($eventKey));
    }

    public function incNowFloor($eventKey)
    {
        $this->redis->incrBy($this->getNowFloorRedisKey($eventKey), 1);
    }
}