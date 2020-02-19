<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/02/18
 * Time: 11:25
 */

namespace App\Api\Service;

use App\Common\Model\CacheModel\GiftModel as GiftCacheModel;
use App\Common\Model\CacheModel\ReplyGiftModel as ReplyGiftCacheModel;
use App\Common\Model\DatabaseModel\ReplyGiftModel as ReplyGiftDatabaseModel;
use App\Common\Model\DatabaseModel\GiftModel as GiftDatabaseModel;
use App\Api\Object\WeChatResponseObject;

class WeChatGiftService
{
    /**
     * @var WeChatGiftService $init
     */
    private static $init;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * @return WeChatGiftService
     */
    public static function instance()
    {
        if (!self::$init) {
            self::$init = new static();
        }
        return self::$init;
    }

    /**
     *
     */
    public function getGiftCode($fromUserName, $toUserName, $giftKey)
    {
        $giftCacheModel = new GiftCacheModel();
        if ($giftCacheModel->getGiftLock($fromUserName, $giftKey)) {
            WeChatApiService::instance()->response(
                $fromUserName,
                $toUserName,
                '礼包系统繁忙，请稍后!',
                WeChatResponseObject::MSG_TYPE_TEXT
            );
        } else {
            $giftCacheModel->setGiftLock($fromUserName, $giftKey);
        }
        $codeData = $giftCacheModel->getGiftCode($giftKey);
        if (!$codeData) {
            $lock_res = $giftCacheModel->setGiftListLock();
            if (!$lock_res) {
                WeChatApiService::instance()->response(
                    $fromUserName,
                    $toUserName,
                    '领取礼包的人数太多，请稍后再试!!~',
                    WeChatResponseObject::MSG_TYPE_TEXT
                );
            }
            $giftDatabaseModel = new GiftDatabaseModel();
            $codeList = $giftDatabaseModel->getList([
                'hd' => $giftKey,
                'state' => 0,
                'page' => 1,
                'pageSize' => 100
            ]);
            if ($codeList) {
                $codeList = array_map('serialize', (array)$codeList);
                $giftCacheModel->setGiftList($codeList);
                $giftCacheModel->delGiftListLock();
                $codeData = unserialize($giftCacheModel->getGiftLock($fromUserName, $giftKey));
                $codeData->code = trim($codeData->code);
            } else {
                $giftCacheModel->delGiftListLock();
                $codeData = [];
            }
        } else {
            $codeData->code = trim($codeData->code);
        }
        return $codeData;
    }

    public function getGiftReply($appid, $giftKey)
    {
        $cacheModel = new ReplyGiftCacheModel();
        $replyList = $cacheModel->getReplyGift($appid);
        if (!$replyList) {
            $dbModel = new ReplyGiftDatabaseModel();
            $replyList = $dbModel->getList(['page' => 1, 'pageSize' => 20, 'app_id' => $appid]);
            if ($replyList) {
                $replyList = $replyList->keyBy('app_id');
            } else {
                $replyList = [0];
            }
            $cacheModel->setReplyGift($appid, $replyList);
        }
    }
}