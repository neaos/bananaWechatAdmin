<?php

namespace App\Common\Model\CacheModel;

use Library\Virtual\Model\CacheModel\AbstractRedisModel;

/**
 * Created by PhpStorm.
 * User: ZhongHao-Zh
 * Date: 2019/10/26
 * Time: 20:18
 */
class GiftModel extends AbstractRedisModel
{
    private function getGiftLockRedisKey($formUserName, $giftKey)
    {
        return "{$formUserName}_{$giftKey}_gift_lock";
    }

    public function setGiftLock($formUserName, $giftKey)
    {

    }

    public function getGiftLock($formUserName, $giftKey)
    {

    }

    public function getGiftCode($giftKey)
    {

    }

    public function setGiftList($giftDataList)
    {
        $setRes = $this->redis->lPush($name, ...$giftDataList);
        if ($setRes) {
            $this->redis->expire($name, 3600 * 2);
            return true;
        }
        return false;
    }

    public function setGiftListLock()
    {
        $this->redis->set($this->gift_code_public_lock_key, 1, ['nx', 'ex' => 40]);
    }

    public function delGiftListLock()
    {
        $this->redis->del($this->gift_code_public_lock_key);
    }

}