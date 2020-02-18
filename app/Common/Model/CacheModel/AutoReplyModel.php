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
class AutoReplyModel extends AbstractRedisModel
{
    private $autoReplyListKey = 'wechat_system_auto_reply_list_';

    /**
     * 获取自动回复列表
     * @param $appid
     * @return array|Collection
     */
    public function getAutReplyListByAppId($appid)
    {
        return unserialize($this->redis->get("{$this->autoReplyListKey}_{$appid}"));
    }

    /**
     * 获取自动回复列表
     * @param $appid
     * @param $list
     */
    public function setAutoReplyListByAppId($appid, $list)
    {
        $this->redis->set("{$this->autoReplyListKey}_{$appid}", serialize($list));
    }
}