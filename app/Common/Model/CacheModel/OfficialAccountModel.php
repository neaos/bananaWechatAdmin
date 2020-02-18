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
class OfficialAccountModel extends AbstractRedisModel
{
    private $officialAccountListKey = 'wechat_system_official_account_list';

    /**
     * 获取公众号列表
     * @return array|Collection
     */
    public function getOfficialAccountList()
    {
        return unserialize($this->redis->get($this->autoReplyListKey));
    }

    /**
     * 获取公众号列表
     * @param $list
     */
    public function setOfficialAccountList($list)
    {
        $this->redis->set($this->autoReplyListKey, serialize($list));
    }
}