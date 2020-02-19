<?php

namespace App\Api\Model\CacheModel;

use Library\Virtual\Model\CacheModel\AbstractRedisModel;

/**
 * Created by PhpStorm.
 * User: ZhongHao-Zh
 * Date: 2019/10/26
 * Time: 20:18
 */
class TextAnswerModel extends AbstractRedisModel
{
    /**
     * @param $appid
     * @param $fromUserName
     * @return string
     */
    private function userAnswerRedisKey($appid, $fromUserName)
    {
        return "{$appid}_{$fromUserName}_answer";
    }

    /**
     * @param $appid
     * @param $fromUserName
     * @return mixed
     */
    public function getUserAnswerData($appid, $fromUserName)
    {
        return unserialize($this->redis->get($this->userAnswerRedisKey($appid, $fromUserName)));
    }

    /**
     * @param $appid
     * @param $fromUserName
     * @param $answerData
     * @return bool
     */
    public function setUserAnswerData($appid, $fromUserName, $answerData)
    {
        return $this->redis->set($this->userAnswerRedisKey($appid, $fromUserName), serialize($answerData));
    }

    /**
     * @param $appid
     * @param $fromUserName
     * @return int
     */
    public function delUserAnswerData($appid, $fromUserName)
    {
        return $this->redis->del($this->userAnswerRedisKey($appid, $fromUserName));
    }
}